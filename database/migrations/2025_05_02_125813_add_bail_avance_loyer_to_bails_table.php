<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBailAvanceLoyerToBailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bails', function (Blueprint $table) {
             $table->double('bail_avance_loyer')->nullable()->after('bail_caution_mnt_ht');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bails', function (Blueprint $table) {
            $table->dropColumn('bail_avance_loyer');
        });
    }
}
