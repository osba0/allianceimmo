<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMailsEnAttenteTable extends Migration
{
    public function up()
    {
        Schema::create('mails_en_attente', function (Blueprint $table) {
            $table->id();
            $table->string('action');  // Cas d’usage Paiement reçu / Creation Mandat / Ajout de bien / Relevé des loyers mensuel ou Expiration de bail
            $table->string('email_destinataire');
            $table->string('email_cc')->nullable();
            $table->string('sujet');
            $table->text('contenu_html');
            $table->text('contenu_text')->nullable();
            $table->string('fichier_joint')->nullable();
            $table->enum('etat', ['en_attente', 'envoye', 'erreur'])->default('en_attente');
            $table->string('template')->nullable(); // nom du template blade
            $table->json('data')->nullable(); // données à injecter dans le template
            $table->text('message_erreur');
            $table->timestamp('envoye_le')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mails_en_attente');
    }
}
