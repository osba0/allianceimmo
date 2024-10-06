<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilialesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filiales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agence_id');
            $table->string('filiale_id')->unique();
            $table->string('filiale_name');
            $table->string('filiale_email')->nullable();
            $table->string('filiale_ind1');
            $table->string('filiale_tel1');
            $table->string('filiale_ind2')->nullable();
            $table->string('filiale_tel2')->nullable();
            $table->string('filiale_adresse')->nullable();
            $table->string('filiale_ville');
            $table->string('filiale_pays');
            $table->string('filiale_logo')->nullable();
            $table->string('filiale_user');
            $table->timestamps();

            $table->foreign('agence_id')->references('id')->on('agences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('filiales');
    }
}
