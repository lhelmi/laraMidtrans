<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;

class mailMidtrans extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($title, $detail)
    {
        $this->title = $title;
        $this->detail = $detail;
    }

    public string $title;
    public string $detail;
    // public array $cc = [];
    // public array $bcc = [];

    /**
     * Get the message envelope.
     */

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
            // cc: $this->cc,
            // bcc: $this->bcc,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.mailMidtrans',
            with : [
                'subject' => $this->title,
                'detail' => $this->detail,
            ]
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
            Attachment::fromPath(storage_path('/app/public/attach.pdf'))
                ->as('attach.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
