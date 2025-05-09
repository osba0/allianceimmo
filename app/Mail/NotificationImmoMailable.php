<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\User;

class NotificationImmoMailable extends Mailable
{
    public $sujet;
    public $contenu_html;
    public $contenu_text;
    public $fichier_joint;
    public $email_cc;
    public $template;
    public $data;

    public function __construct($sujet, $contenu_html, $contenu_text = null, $fichier_joint = null, $email_cc = null, $template= null,
                        $data= null)
    {
        $this->contenu_html = $contenu_html;
        $this->contenu_text = $contenu_text;
        $this->fichier_joint = $fichier_joint;
        $this->email_cc = $email_cc;
        $this->template = $template;
        $this->data = $data;

        // get agence_id
        $json_data = json_decode($this->data, true);

        // Ajouter les emails des administrateurs
        $adminsUsers = User::where('is_admin', true)->where("agence_id",$json_data['agence_id'])->pluck('email')->toArray();

        // Fusionner avec les éventuels emails fournis manuellement
        $this->email_cc = array_filter(array_merge((array) $email_cc, $adminsUsers));
    }

    public function build()
    {

        $mail = $this->subject($this->sujet);

        //  Pièce jointe
        if ($this->fichier_joint && file_exists(public_path($this->fichier_joint))) {
            $mail->attach(public_path($this->fichier_joint));
        }

        //  Email CC
        if ($this->email_cc) {
            $mail->cc($this->email_cc);
        }

       // var_dump(json_decode($this->data, true)); die();

        $mail->view('emails.'.$this->template, [
                     'data' => json_decode($this->data, true)
        ]);

        return $mail;


    }
}
