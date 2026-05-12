<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_category_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('name')->nullable();
            $table->bigInteger('phone')->nullable();
            $table->string('image')->nullable();
            $table->string('address')->nullable();
            $table->string('description')->nullable();
            $table->mediumInteger('rating')->nullable();
            $table->mediumText('approx_time_delivery')->nullable();
            $table->longText('license_document')->nullable();
            $table->longText('vat_documents')->nullable();
            $table->string('land_mark')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->decimal('restaurant_charges')->nullable();
            $table->mediumText('delivery_charge_type')->nullable();
            $table->decimal('base_delivery_charge', 8, 2)->nullable();
            $table->mediumText('base_delivery_distance')->nullable();
            $table->decimal('extra_delivery_charge', 8, 2)->nullable();
            $table->mediumText('extra_delivery_distance')->nullable();
            $table->decimal('delivery_charge')->nullable();
            $table->decimal('commission_rate')->nullable();
            $table->decimal('tax')->nullable();
            $table->bigInteger('delivery_radius')->nullable();
            $table->string('delivery_type')->nullable();
            $table->string('min_order_price')->nullable();
            $table->tinyInteger('is_schedule')->default('0')->nullable();
            $table->longText('schedule_data')->nullable();
            $table->boolean('is_deleted')->default('0')->nullable();
            $table->boolean('is_accepted')->default('0')->nullable();
            $table->boolean('is_popular')->default('0')->nullable();
            $table->boolean('is_veg')->default('0')->nullable();
            $table->boolean('is_recommended')->default('0')->nullable();
            $table->boolean('is_freedelivery')->default('0')->nullable();
            $table->boolean('is_active')->default('0')->nullable();
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
        Schema::dropIfExists('restaurants');
    }
}
