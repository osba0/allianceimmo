<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgenceIdToLocatairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('locataires', function (Blueprint $table) {
            $table->unsignedBigInteger('agence_id')->nullable()->after('locat_id');

            $table->foreign('agence_id')
                ->references('id')
                ->on('agences')
                ->onDelete('set null'); // ou 'cascade' si tu veux tout supprimer avec l'agence
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locataires', function (Blueprint $table) {
            $table->dropForeign(['agence_id']);
            $table->dropColumn('agence_id');
        });
    }
}
