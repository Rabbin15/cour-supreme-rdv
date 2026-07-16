<?php

namespace App\Mail;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RendezVousConfirme extends Mailable
{
    use Queueable, SerializesModels;

    public $rdv;
    public $statut;

    public function __construct(RendezVous $rdv, $statut)
    {
        $this->rdv = $rdv;
        $this->statut = $statut;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre rendez-vous à la Cour Suprême du Bénin',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rdv-confirme',
        );
    }
}