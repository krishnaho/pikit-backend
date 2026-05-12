<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->unsignedBigInteger('item_category_id')->nullable();
            $table->mediumText('name')->nullable();
            $table->string('image')->nullable();
            $table->string('description')->nullable();
            $table->decimal('market_price', 8, 2)->nullable();
            $table->decimal('selling_price', 8, 2)->nullable();
            $table->decimal('commision_rate', 4, 2)->nullable();
            $table->mediumInteger('min_quantity')->nullable();
            $table->mediumInteger('max_quantity')->nullable();
            $table->boolean('is_schedule')->default(0)->nullable();
            $table->string('schedule_data')->nullable();
            $table->boolean('is_popular')->default(0)->nullable();
            $table->boolean('is_top_item')->default(0)->nullable();
            $table->boolean('is_recommended')->default(0)->nullable();
            $table->boolean('is_veg')->default(0)->nullable();
            $table->boolean('is_active')->default(0)->nullable();
            $table->string('out_stock_time_at')->nullable();
            $table->string('out_of_stock_time')->nullable();
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
        Schema::dropIfExists('items');
    }
}
