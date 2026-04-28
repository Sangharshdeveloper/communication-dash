<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Magic Login Tokens Table
     * CBUAE Compliance: Stores only hashed tokens, full audit trail
     */
    public function up(): void
    {
        Schema::create('magic_login_tokens', function (Blueprint $table) {
            $table->id();

            // User reference
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Security: Only store SHA-256 hash, never raw token
            $table->string('token_hash', 64)->unique()->comment('SHA-256 hash of the raw token');

            // Expiry & usage control
            $table->timestamp('expires_at')->comment('Token expires 10-15 min after creation');
            $table->boolean('is_used')->default(false)->comment('One-time use only');
            $table->timestamp('used_at')->nullable();

            // Audit trail - CBUAE requirement
            $table->string('created_ip', 45)->comment('IP address that requested the link');
            $table->string('used_ip', 45)->nullable()->comment('IP address that used the link');
            $table->text('created_user_agent')->nullable();
            $table->text('used_user_agent')->nullable();

            // Device fingerprint for suspicious activity detection
            $table->string('device_fingerprint')->nullable();
            $table->boolean('otp_required')->default(false)->comment('OTP required if IP/device mismatch');
            $table->string('otp_hash', 64)->nullable()->comment('Hashed OTP for secondary verification');
            $table->timestamp('otp_expires_at')->nullable();
            $table->boolean('otp_verified')->default(false);

            // Invalidation tracking
            $table->string('invalidated_reason')->nullable(); // 'expired', 'used', 'manually_revoked'
            $table->timestamp('invalidated_at')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_used', 'expires_at']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('magic_login_tokens');
    }
};
