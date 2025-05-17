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
        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('otp_type', ['email', 'phone', 'password_reset', 'two_factor', 'forgot_password', 'locked_account']);
            // 'email' for email OTP, 'phone' for phone OTP, 'password_reset' for password reset OTP, 'two_factor' for two-factor authentication
            $table->string('recipient'); // Stores email/phone based on type
            $table->string('otp');
            $table->timestamp('expires_at');
            $table->timestamp('verified_at')->nullable();
            $table->boolean('used')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'otp_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_verifications');
    }
};
