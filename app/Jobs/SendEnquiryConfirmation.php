<?php

namespace App\Jobs;

use App\Mail\EnquiryReceived;
use App\Models\Enquiry;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

/**
 * Sends the confirmation email for a new enquiry.
 *
 * Dispatched to run after the response has gone back to the visitor, so the
 * form answers instantly and a slow or broken mail server never shows up as
 * a failed enquiry. No queue worker is needed - shared hosting has none.
 *
 * A failure is logged and leaves `confirmation_sent_at` empty; the enquiry
 * itself is already saved, and the agent can resend from the inbox.
 */
class SendEnquiryConfirmation
{
    use Dispatchable;

    /** Confirmations one address can receive per hour, whatever is submitted. */
    private const PER_ADDRESS_PER_HOUR = 3;

    /**
     * @param  bool  $resend  the agent asked for it again - send even if one already went
     */
    public function __construct(
        public readonly int $enquiryId,
        public readonly bool $resend = false,
    ) {
    }

    /** Queue the confirmation for a new enquiry, if it should get one. */
    public static function for(Enquiry $enquiry): void
    {
        if (static::shouldSend($enquiry)) {
            static::dispatchAfterResponse($enquiry->id);
        }
    }

    public static function shouldSend(Enquiry $enquiry): bool
    {
        return (bool) config('mail.enquiry_confirmation')
            && static::mailIsConfigured()
            && filled($enquiry->email);
    }

    /**
     * The `log` mailer writes email to a file and reports success. Treating
     * that as sent would tell the agent a client was emailed when nobody was,
     * so until real mail settings exist, nothing is marked as sent.
     */
    public static function mailIsConfigured(): bool
    {
        return config('mail.default') !== 'log';
    }

    /** @return bool whether the email was handed to the mail server */
    public function handle(): bool
    {
        $enquiry = Enquiry::with('property')->find($this->enquiryId);

        if (! $enquiry || ! filled($enquiry->email) || ! static::mailIsConfigured()) {
            return false;
        }

        // Runs at most once per enquiry unless the agent resends on purpose.
        if ($enquiry->confirmation_sent_at && ! $this->resend) {
            return false;
        }

        // Stops the public form being used to flood one inbox.
        $key = 'enquiry-confirmation:'.sha1(mb_strtolower($enquiry->email));
        if (RateLimiter::tooManyAttempts($key, self::PER_ADDRESS_PER_HOUR)) {
            return false;
        }

        try {
            Mail::to($enquiry->email, $enquiry->name)->send(new EnquiryReceived($enquiry));
        } catch (Throwable $e) {
            report($e);

            return false;
        }

        RateLimiter::hit($key, 3600);
        $enquiry->forceFill(['confirmation_sent_at' => now()])->saveQuietly();

        return true;
    }
}
