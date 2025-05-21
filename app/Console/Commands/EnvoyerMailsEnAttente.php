<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MailEnAttente;
use App\Mail\NotificationImmoMailable;
use Illuminate\Support\Facades\Mail;

class EnvoyerMailsEnAttente extends Command
{
    protected $signature = 'emails:envoyer';
    protected $description = 'Envoie les emails de paiement en attente';

    public function handle()
    {
        \Log::info('Sent mail...');
        $mails = MailEnAttente::where('etat', 'en_attente')->limit(20)->get();
        $totalMailEnvoye = 0;
        foreach ($mails as $mail) {
            try {
                Mail::to($mail->email_destinataire)
                    ->send(new NotificationImmoMailable(
                        $mail->sujet,
                        $mail->contenu_html,
                        $mail->contenu_text,
                        $mail->fichier_joint,
                        $mail->email_cc,
                        $mail->template,
                        $mail->data
                    ));

                $mail->update([
                    'etat' => 'envoye',
                    'envoye_le' => now()
                ]);
                $totalMailEnvoye++;
            } catch (\Exception $e) {
                \Log::info('Erreur envoi mail ID '.$mail->id.' : '.$e->getMessage());
                print_r('Erreur envoi mail ID '.$mail->id.' : '.$e->getMessage());

                $mail->update(['message_erreur' => $e->getMessage(), 'etat' => 'en_attente' ]);
            }
        }

        $this->info("✅ $totalMailEnvoye mail(s) envoyé(s) avec succès.");
    }
}
