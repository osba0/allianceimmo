<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->string('oper_id')->unique(); 
            $table->string('oper_sens'); // Debit ou Credit
            $table->string('oper_type'); 
            $table->string('oper_type_autre'); 
            $table->double('oper_montant');
            $table->string('oper_id_bail')->nullable(); 
            $table->string('oper_id_charge')->nullable();  
            $table->string('oper_id_versement_proprio')->nullable();
            $table->string('oper_note');  
            $table->string('oper_user');
            $table->string('oper_reserve_1')->nullable();
            $table->string('oper_reserve_2')->nullable();
            $table->string('oper_reserve_3')->nullable();
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
        Schema::dropIfExists('operations');
    }
}
