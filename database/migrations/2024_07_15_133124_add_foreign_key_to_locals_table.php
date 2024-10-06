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
            // Si tu as d'autres clés étrangères à ajouter ou d'autres modifications
            // Évite de redéfinir la clé `bien_id` déjà présente dans `CreateLocalsTable`
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
            // Reviens en arrière seulement si tu as d'autres modifications
        });
    }
}
