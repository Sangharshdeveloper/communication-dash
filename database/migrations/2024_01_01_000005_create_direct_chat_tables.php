<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('direct_message_attachments');
        Schema::dropIfExists('direct_messages');
        Schema::dropIfExists('direct_chat_sessions');

        Schema::create('direct_chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_token', 64)->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->string('customer_ref')->nullable()->comment('External customer ID from URL param');
            $table->string('status')->default('active');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index('session_token');
            $table->index(['customer_id', 'agent_id']);
        });

        Schema::create('direct_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('direct_chat_sessions')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('body')->nullable();
            $table->string('type')->default('text');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['session_id', 'created_at']);
        });

        Schema::create('direct_message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('direct_messages')->onDelete('cascade');
            $table->string('original_name');
            $table->string('stored_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_message_attachments');
        Schema::dropIfExists('direct_messages');
        Schema::dropIfExists('direct_chat_sessions');
    }
};