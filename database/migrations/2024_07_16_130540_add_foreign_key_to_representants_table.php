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
            // Évite de redéfinir la clé `repr_id_proprio` déjà présente dans `CreateRepresentantsTable`
            // S'il y a d'autres modifications, tu peux les inclure ici
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
            // Seulement si d'autres modifications doivent être annulées
        });
    }
}
