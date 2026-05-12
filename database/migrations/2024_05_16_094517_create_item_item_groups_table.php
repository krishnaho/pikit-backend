<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemItemGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_item_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_group_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->foreign('item_group_id')->references('id')->on('item_groups');
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
        Schema::dropIfExists('item_item_group');
    }
}
