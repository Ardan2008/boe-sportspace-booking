<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RejectBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $reason;
    public $actionDate;

    public function __construct($booking, $reason, $actionDate)
    {
        $this->booking = $booking;
        $this->reason = $reason;
        $this->actionDate = $actionDate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Informasi Penolakan Booking - BOE Sport Space',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reject',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
