<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->mediumText('name')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('restaurant_category_id')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_schedule')->default('0')->nullable();
            $table->longText('schedule_data')->nullable();
            $table->boolean('is_active')->default('1')->nullable();
            $table->string('out_stock_time_at')->nullable();
            $table->string('out_of_stock_time')->nullable();
        
            $table->boolean('is_deleted')->default('0')->nullable();
            $table->integer('order_column')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_categories');
    }
}
