<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddonCategoryItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addon_category_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('addon_category_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->foreign('addon_category_id')->references('id')->on('addon_categories');
            $table->foreign('item_id')->references('id')->on('items');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addon_category_item');
    }
}
