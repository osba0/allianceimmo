<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailEnAttente extends Model
{
    use HasFactory;
    protected $table = 'mails_en_attente';

    protected $fillable = [
        'action',
        'email_destinataire',
        'email_cc',
        'sujet',
        'contenu_html',
        'contenu_text',
        'fichier_joint',
        'etat',
        'envoye_le',
        'template',
        'data',
        'message_erreur'
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
