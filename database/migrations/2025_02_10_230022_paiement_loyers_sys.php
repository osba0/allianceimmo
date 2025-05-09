<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaiementLoyersSys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('paiement_loyers_sys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bail_id')->constrained('bails')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Ajout de l'utilisateur qui effectue l'opération
            $table->decimal('montant', 10, 2);
            $table->date('date_paiement');
            $table->enum('mode_paiement', ['espèces', 'virement', 'chèque', 'mobile_money']);
            $table->date('periode_paiement')->comment('Mois concerné par le paiement');
            $table->enum('statut', ['partiel', 'complet', 'avance', 'arriéré']);
            $table->decimal('solde', 10, 2)->default(0)->comment('Solde restant dû ou avance');
            $table->decimal('penalite', 10, 2)->default(0)->comment('Pénalité appliquée en cas de retard');
            $table->string('reference', 50)->unique()->nullable()->comment('Référence du paiement');
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
        Schema::dropIfExists('paiement_loyers_sys');
    }
}
