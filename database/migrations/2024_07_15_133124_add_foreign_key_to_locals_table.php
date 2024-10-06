<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToLocalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locals', function (Blueprint $table) {
            $table->dropForeign(['bien_id']);
            $table->foreign('bien_id')
              ->references('bien_id')
              ->on('biens')
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
        Schema::table('locals', function (Blueprint $table) {
            $table->dropForeign(['bien_id']);
            $table->foreign('bien_id')
              ->references('bien_id')
              ->on('biens');
        });
    }
}
