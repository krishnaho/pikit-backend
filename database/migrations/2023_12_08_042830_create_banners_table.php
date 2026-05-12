<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('restaurant_category_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->mediumText('name')->nullable();
            $table->mediumText('type')->nullable();
            $table->string('image')->nullable();
            $table->mediumText('latitude')->nullable();
            $table->mediumText('longitude')->nullable();
            $table->mediumInteger('radius')->nullable();
            $table->boolean('is_active')->default(0)->nullable();
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
        Schema::dropIfExists('banners');
    }
}
