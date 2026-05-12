<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('unique_order_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('restaurant_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('order_status_id')->nullable();
            $table->mediumText('agent_phone')->nullable();
            $table->string('order_comment')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('payment_status')->nullable();
            $table->integer('transaction_id')->nullable();
            $table->longText('payment_gateway_info')->nullable();
            $table->decimal('sub_total', 8, 2)->nullable();
            $table->integer('restaurant_charges')->nullable();
            $table->decimal('surge_fee', 8, 2)->nullable();
            $table->integer('restaurant_total')->nullable();
            $table->decimal('convenience_fee', 8, 2)->nullable();
            $table->decimal('platform_fee', 8, 2)->nullable();

            $table->integer('tax')->nullable();
            $table->decimal('total_commission', 8, 2)->nullable();
            $table->decimal('delivery_charge', 8, 2)->nullable();
            $table->mediumText('coupon_code')->nullable();
            $table->decimal('coupon_amount', 8, 2)->nullable();
            $table->decimal('walletamount', 8, 2)->nullable();
            $table->decimal('total', 8, 2)->nullable();
            $table->decimal('payable', 8, 2)->nullable();
            $table->decimal('payout_amount', 8, 2)->nullable();
            $table->longText('address')->nullable();
            $table->mediumText('latitude')->nullable();
            $table->mediumText('longitude')->nullable();
            $table->mediumText('landmark')->nullable();
            $table->decimal('tip_amount', 8, 2)->nullable();
            $table->boolean('is_schedule')->default(0)->nullable();
            $table->date('schedule_date')->nullable();
            $table->mediumText('schedule_time')->nullable();
            $table->mediumText('order_prepairing_time')->nullable();
            $table->string('order_placed_at')->nullable();
            $table->string('order_accepted_at')->nullable();
            $table->string('formatted_remaining_time')->nullable();
            $table->string('order_assigned_at')->nullable();
            $table->string('order_ready_to_pickup_at')->nullable();
            $table->string('order_picked_up_at')->nullable();
            $table->string('order_delivered_at')->nullable();
            $table->string('order_cancelled_at')->nullable();
            $table->string('need_more_time_at')->nullable();
            $table->string('cancellation_reason')->nullable();

            $table->boolean('is_payout_released')->default(0)->nullable();
            $table->boolean('is_self_pickup')->default(0)->nullable();
            $table->boolean('is_express')->default(0)->nullable();
            $table->boolean('is_refunded')->default(0)->nullable();
            



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
        Schema::dropIfExists('orders');
    }
}
