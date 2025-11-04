<?php

namespace App\Mail;

use App\Models\TicketSoporteGalen;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketSoporteGalenNuevo extends Mailable
{
    use Queueable, SerializesModels;

    public TicketSoporteGalen $ticket;

    public function __construct(TicketSoporteGalen $ticket)
    {
        $this->ticket = $ticket;
    }

    public function build()
    {
        return $this->subject('Nuevo ticket de soporte (Admin → Galen)')
            ->view('emails.ticket-soporte-galen-nuevo');
    }
}
