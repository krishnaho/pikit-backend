<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->mediumText('name')->nullable();
            $table->string('image')->nullable();
            $table->mediumText('description')->nullable();
            $table->mediumText('coupon_code')->nullable();
            $table->mediumText('discount_type')->nullable();
            $table->mediumText('latitude')->nullable();
            $table->mediumText('longitude')->nullable();
            $table->mediumInteger('radius')->nullable();
            $table->decimal('coupon_discount', 8, 2)->nullable();
            $table->string('coupon_type')->nullable();
            $table->string('sub_total_message')->nullable();
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->integer('max_count')->nullable();
            $table->integer('min_count')->nullable();
            $table->mediumInteger('max_sub_total')->nullable();
            $table->mediumInteger('min_sub_total')->nullable();
            $table->decimal('max_discount', 8, 2)->nullable();
            $table->integer('max_count_per_user')->nullable();
            $table->integer('max_use_count')->nullable();
            $table->string('user_type')->nullable();
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
        Schema::dropIfExists('coupons');
    }
}
