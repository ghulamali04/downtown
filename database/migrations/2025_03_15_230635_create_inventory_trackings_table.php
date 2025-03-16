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
        Schema::create('inventory_trackings', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('inventory_item_id')->index();
            $table->foreign('inventory_item_id', 'fk_inventory_trackings_inventory_id')
            ->references('id')
            ->on('inventory_items')
            ->onDelete('cascade');
            $table->date('date');
            $table->text('description')->nullable();
            $table->enum('type', ['purchase', 'used', 'wasted', 'returned']);
            $table->decimal('amount', 19, 2)->default(0);
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id', 'fk_inventory_trackings_user_id')
            ->references('id')
            ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_trackings');
    }
};
