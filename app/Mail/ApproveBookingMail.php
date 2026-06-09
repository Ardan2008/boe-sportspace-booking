<?php

namespace App\Mail;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApproveBookingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $actionDate;

    public function __construct($booking, $actionDate)
    {
        $this->booking = $booking;
        $this->actionDate = $actionDate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Persetujuan Booking - BOE Sport Space',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.approve',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.kuitansi', ['booking' => $this->booking, 'actionDate' => $this->actionDate]);
        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'Kuitansi-Booking-' . $this->booking->id . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
