<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OperationLoyer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('operations_loyers', function (Blueprint $table) {
            $table->id();
            $table->string('oper_paiement_id'); 
            $table->double('oper_montant'); 
            $table->date('oper_date');
            $table->date('oper_user');
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
        //
    }
}
