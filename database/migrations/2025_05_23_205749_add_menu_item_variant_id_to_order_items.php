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
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_item_variant_id')
            ->nullable()
            ->index();
            $table->foreign('menu_item_variant_id', 'fk_order_item_menu_item_variant_id')
            ->references('id')
            ->on('menu_item_variants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign('fk_order_item_menu_item_variant_id');
            $table->dropIndex(['menu_item_variant_id']);
            $table->dropColumn([
                'menu_item_variant_id'
            ]);
        });
    }
};
