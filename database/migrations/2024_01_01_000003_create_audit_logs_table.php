<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit Logs Table - CBUAE Compliance Requirement
     * All actions must be logged with IP and timestamp
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Actor
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email')->nullable()->comment('Email even if user not yet authenticated');

            // Action details
            $table->string('action')->comment('link_generated, email_sent, login_success, login_failed, logout, otp_sent, otp_verified, token_expired, token_revoked, suspicious_activity');
            $table->string('status')->default('success')->comment('success, failure, warning');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable()->comment('Additional context data');

            // Network info - CBUAE requirement
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->string('country_code', 3)->nullable()->comment('Should be AE for UAE compliance');

            // Request context
            $table->string('request_id')->nullable()->comment('Unique request identifier for tracing');
            $table->string('session_id')->nullable();

            // Immutable timestamp - cannot be modified
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'action', 'created_at']);
            $table->index(['email', 'created_at']);
            $table->index(['action', 'status', 'created_at']);
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
