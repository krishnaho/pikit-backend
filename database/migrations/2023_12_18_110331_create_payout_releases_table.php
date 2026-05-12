<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayoutReleasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payout_releases', function (Blueprint $table) {
            $table->id();
            $table->string('order_ids')->nullable();
            $table->unsignedBigInteger('restaurant_id')->nullable();
            $table->decimal('payout_amount', 8, 2)->nullable();
            $table->integer('payout_released_by')->nullable();
            $table->string('message')->nullable();
            $table->timestamps();
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payout_releases');
    }
}
