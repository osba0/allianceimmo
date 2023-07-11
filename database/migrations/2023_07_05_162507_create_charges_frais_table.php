<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChargesFraisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charges_frais', function (Blueprint $table) {
            $table->id();
            $table->string('charge_id')->unique(); 
            $table->string('charge_type'); 
            $table->string('charge_type_autre')->nullable(); 
            $table->double('charge_montant');
            $table->string('charge_id_proprio')->nullable(); 
            $table->string('charge_id_bien')->nullable(); 
            $table->string('charge_id_local')->nullable(); 
            $table->string('charge_note')->nullable();  
            $table->string('charge_user')->nullable();
            $table->text('charge_facture')->nullable(); 
            $table->string('charge_reserve_1')->nullable(); 
            $table->string('charge_reserve_2')->nullable(); 
            $table->string('charge_reserve_3')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charges_frais');
    }
}
