<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnUrlQuittance extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('paiements_loyers', function (Blueprint $table) {
            $table->string('paiements_url_quittance')->nullable()->after('paiement_cloture');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('paiements_loyers', function (Blueprint $table) {
           $table->dropColumn('paiements_url_quittance');
        });
    }
}
