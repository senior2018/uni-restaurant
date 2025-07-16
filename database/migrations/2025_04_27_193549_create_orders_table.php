<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('staff_id')->nullable()->constrained('users');
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'preparing', 'delivered', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['cash', 'mobile_money', 'card']);
            $table->text('delivery_location');
            $table->text('staff_notes')->nullable();
            $table->boolean('cancellation_requested')->default(false);
            $table->boolean('cancellation_request_seen')->default(false);
            $table->text('cancellation_reason')->nullable();
            $table->string('cancelled_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('status'); // Faster status filtering
            $table->index('created_at'); // For order history sorting
            $table->index('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
