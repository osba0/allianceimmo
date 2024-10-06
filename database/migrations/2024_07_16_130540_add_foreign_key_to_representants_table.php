<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToRepresentantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('representants', function (Blueprint $table) {
            $table->dropForeign(['repr_id_proprio']);
            $table->foreign('repr_id_proprio')
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
        Schema::table('representants', function (Blueprint $table) {
            Schema::table('biens', function (Blueprint $table) {
                $table->dropForeign(['repr_id_proprio']);
                $table->foreign('repr_id_proprio')
                  ->references('proprio_id')
                  ->on('proprietaires');
            });
        });
    }
}
