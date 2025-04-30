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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('order_id')->constrained('orders');
            $table->tinyInteger('rating')->unsigned()->between(1, 5);
            $table->text('comment')->nullable();
            $table->foreignId('meal_id')->constrained('meals');
            $table->foreignId('response_staff_id')->nullable()->constrained('users');
            $table->text('response_comment')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['meal_id', 'rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
