<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersAgenceFilialeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('agence_id')->nullable();
            $table->unsignedBigInteger('filiale_id')->nullable();

            $table->foreign('agence_id')->references('id')->on('agences')->onDelete('cascade');
            $table->foreign('filiale_id')->references('id')->on('filiales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['agence_id']);
            $table->dropForeign(['filiale_id']);
            $table->dropColumn(['agence_id', 'filiale_id']);
        });
    }
}
