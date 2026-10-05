<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * "We have your enquiry" - sent to the person who made it.
 *
 * Built entirely from the enquiry and the listing record, never by a model:
 * this goes to a client unread, so every word in it has to be right. It
 * deliberately does not repeat the visitor's free-text message - anyone can
 * type any address into a public form, and echoing their text would let the
 * form be used to send arbitrary content to strangers.
 *
 * It is sent in the agent's name, and replies go to the agent's own inbox.
 */
class EnquiryReceived extends Mailable
{
    public function __construct(public readonly Enquiry $enquiry)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), config('agent.name').' - '.config('agent.agency')),
            replyTo: [new Address(config('agent.email'), config('agent.name'))],
            subject: $this->subjectLine(),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.enquiry-received', with: [
            'enquiry'  => $this->enquiry,
            'property' => $this->enquiry->property?->is_published ? $this->enquiry->property : null,
            'agent'    => config('agent'),
            'whatsapp' => 'https://wa.me/'.config('agent.whatsapp'),
        ]);
    }

    private function subjectLine(): string
    {
        return match (true) {
            $this->enquiry->type === 'appraisal'    => 'Your free appraisal request - '.config('agent.agency'),
            $this->enquiry->property !== null       => 'Your enquiry about '.$this->enquiry->property->title,
            default                                 => 'Thanks for getting in touch - '.config('agent.agency'),
        };
    }
}
