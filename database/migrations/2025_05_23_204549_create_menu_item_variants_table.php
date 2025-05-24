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
        Schema::create('menu_item_variants', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name');
            $table->decimal('current_price', 19, 2)->default(0);
            $table->unsignedBigInteger('menu_item_id')->index();
            $table->foreign('menu_item_id', 'fk_menu_item_variant_menu_item_id')
            ->references('id')
            ->on('menu_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_variants');
    }
};
