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
        Schema::table('menu_items', function (Blueprint $table) {
            $table->unsignedBigInteger('menu_category_id')
            ->nullable()
            ->index();
            $table->foreign('menu_category_id', 'fk_menu_item_menu_category_id')
            ->references('id')
            ->on('menu_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign('fk_menu_item_menu_category_id');
            $table->dropIndex(['menu_category_id']);
            $table->dropColumn([
                'menu_category_id'
            ]);
        });
    }
};
