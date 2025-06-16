<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnOperationCaissier extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->enum('oper_statut', ['en_attente', 'valide', 'rejete'])->default('en_attente')->after('oper_note');
            $table->text('oper_motif_rejet')->nullable()->after('oper_statut');
            $table->timestamp('oper_date_validation')->nullable()->after('oper_motif_rejet');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operations', function (Blueprint $table) {
           $table->dropColumn('oper_statut');
           $table->dropColumn('oper_motif_rejet');
           $table->dropColumn('oper_date_validation');
        });
    }
}
