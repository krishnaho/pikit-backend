<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->mediumText('name')->nullable();
            $table->unsignedBigInteger('addon_category_id')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->boolean('is_active')->default(0)->nullable();
            $table->string('out_stock_time_at')->nullable();
            $table->string('out_of_stock_time')->nullable();
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
        Schema::dropIfExists('addons');
    }
}
