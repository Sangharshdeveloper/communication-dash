<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds a `mobile` column to the users table so customers can be identified
 * by mobile number (primary identifier for the public chat-link API).
 *
 * Run:  php artisan migrate
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'mobile')) {
                // Put it after `email` if the column exists there, otherwise just append
                $table->string('mobile', 25)
                      ->nullable()
                      ->after(Schema::hasColumn('users', 'email') ? 'email' : 'id');

                $table->index('mobile');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'mobile')) {
                $table->dropIndex(['mobile']);
                $table->dropColumn('mobile');
            }
        });
    }
};
