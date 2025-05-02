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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('order_id')->constrained('orders');
            $table->text('reason');
            $table->foreignId('staff_id')->nullable()->constrained('users');
            $table->text('staff_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->boolean('resolved')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index('resolved'); // Faster unresolved alerts query
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};
