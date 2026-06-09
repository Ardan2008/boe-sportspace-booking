<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class BookingApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $pdfOutput;

    /**
     * Create a new message instance.
     */
    public function __construct($booking, $pdfOutput)
    {
        $this->booking = $booking;
        $this->pdfOutput = $pdfOutput;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Konfirmasi Persetujuan Booking - BOE Sport Space',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking_approved',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfOutput, 'Kwitansi_BOE_' . $this->booking->id . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
