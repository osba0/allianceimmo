<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToBiensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->dropForeign(['bien_proprio']);
            $table->foreign('bien_proprio')
              ->references('proprio_id')
              ->on('proprietaires')
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
        Schema::table('biens', function (Blueprint $table) {
            $table->dropForeign(['bien_proprio']);
            $table->foreign('bien_proprio')
              ->references('proprio_id')
              ->on('proprietaires');
        });
    }
}
