<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyItemItemGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('item_item_group', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign('item_item_group_item_id_foreign');

            // Add the new foreign key with ON DELETE CASCADE
            $table->foreign('item_id')
                  ->references('id')
                  ->on('items')
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
        Schema::table('item_item_group', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign('item_item_group_item_id_foreign');

            // Restore the old foreign key if needed (modify as per your original constraint)
            $table->foreign('item_id')
                  ->references('id')
                  ->on('items');
        });
    }
}
