<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->mediumtext('address')->nullable();
            $table->mediumtext('latitude')->nullable();
            $table->mediumtext('longitude')->nullable();
            $table->mediumtext('house')->nullable();
            $table->mediumtext('landmark')->nullable();
            $table->timestamp('deleted_at')->nullable();
            // $table->mediumtext('tag')->nullable();
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
        Schema::dropIfExists('addresses');
    }
}
