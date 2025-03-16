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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('customer_id')
            ->nullable()
            ->index();
            $table->foreign('customer_id', 'fk_order_customer_id')
            ->references('id')
            ->on('customers');
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id', 'fk_order_user_id')
            ->references('id')
            ->on('users');
            $table->enum('type', ['delivery', 'pickup', 'dining'])->default('dining');
            $table->string('table_number')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->text('instructions')->nullable();
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
