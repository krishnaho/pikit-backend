<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyBannerRestaurantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banner_restaurant', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign('banner_restaurant_banner_id_foreign');

            // Recreate the foreign key with ON DELETE CASCADE
            $table->foreign('banner_id')
                  ->references('id')
                  ->on('banners')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('banner_restaurant', function (Blueprint $table) {
            // Drop the foreign key with cascade
            $table->dropForeign('banner_restaurant_banner_id_foreign');

            // Restore the original foreign key without cascade
            $table->foreign('banner_id')
                  ->references('id')
                  ->on('banners');
        });
    }
}
