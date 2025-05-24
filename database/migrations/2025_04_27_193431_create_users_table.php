<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone')->nullable()->unique();
            $table->string('permanent_location')->nullable();
            $table->enum('role', ['customer', 'staff', 'admin', 'super_admin'])->default('customer');
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('last_failed_attempt')->nullable();
            $table->timestamp('locked_at')->nullable()->after('last_failed_attempt');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
