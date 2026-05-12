<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->integer('radius')->nullable();
            $table->mediumText('delivery_charge_type')->nullable();
            $table->decimal('base_delivery_charge', 8, 2)->nullable();
            $table->mediumText('base_delivery_distance')->nullable();
            $table->decimal('extra_delivery_charge', 8, 2)->nullable();
            $table->mediumText('extra_delivery_distance')->nullable();
            $table->decimal('delivery_charge', 8, 2)->nullable();
            $table->decimal('surge_fee',8,4)->nullable();
            $table->tinyInteger('is_surge')->default(0)->nullable();
            $table->tinyInteger('is_active')->default(0)->nullable();
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
        Schema::dropIfExists('cities');
    }
}
