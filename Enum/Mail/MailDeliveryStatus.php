<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Enum\Mail;

/**
 * Lifecycle of one delivery — one message handed to one address.
 *
 * Why an explicit status next to the existing `sent` timestamp: `sent` answers only
 * "did SMTP accept it?". It cannot express "we are sending right now", "the message
 * came back later" or "the send was cancelled", so every caller invented its own
 * answer to "may I (re)send?" — the root of the repeatedly re-introduced
 * "reported as sent although nothing left" bug.
 *
 * - QUEUED:    recorded, nothing handed to SMTP yet.
 * - SENDING:   handed to SMTP, result not written yet. Stays this way when the process
 *              dies mid-send → "uncertain", never resent automatically (that is exactly
 *              how 17 duplicate payment confirmations happened on 2026-08-21).
 * - SENT:      SMTP accepted the message. `sent` timestamp is filled.
 * - FAILED:    SMTP refused it (or rendering failed). Automatic retry is allowed.
 * - BOUNCED:   accepted, then returned by the recipient's server (DSN).
 * - CANCELLED: a queued delivery the team called off before sending.
 */
enum MailDeliveryStatus: string
{
    case QUEUED = 'queued';
    case SENDING = 'sending';
    case SENT = 'sent';
    case FAILED = 'failed';
    case BOUNCED = 'bounced';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::QUEUED    => 'Čeká na odeslání',
            self::SENDING   => 'Odesílá se',
            self::SENT      => 'Odesláno',
            self::FAILED    => 'Nedoručeno',
            self::BOUNCED   => 'Vráceno',
            self::CANCELLED => 'Zrušeno',
        };
    }

    /** Icon name for `<twig:ux:icon>`; only names present in `assets/icons/tabler/`. */
    public function iconifyName(): string
    {
        return match ($this) {
            self::QUEUED    => 'tabler:clock',
            self::SENDING   => 'tabler:send',
            self::SENT      => 'tabler:check',
            self::FAILED    => 'tabler:alert-triangle',
            self::BOUNCED   => 'tabler:mail-off',
            self::CANCELLED => 'tabler:ban',
        };
    }

    /** Nothing more will happen to this delivery on its own. */
    public function isFinal(): bool
    {
        return match ($this) {
            self::SENT, self::FAILED, self::BOUNCED, self::CANCELLED => true,
            self::QUEUED, self::SENDING                              => false,
        };
    }

    /**
     * May an automatic sender (cron, queue) try this delivery again?
     *
     * Only a refused send may be retried. SENDING means "we do not know whether it
     * went out" — a retry could deliver the message twice, so a human decides.
     */
    public function allowsAutomaticRetry(): bool
    {
        return self::FAILED === $this;
    }

    /**
     * Does this delivery stop an automatic sender from creating another one for the
     * same purpose? True for everything except a refused send — including SENDING,
     * which is the "uncertain" case.
     */
    public function blocksAutomaticResend(): bool
    {
        return self::FAILED !== $this;
    }

    /** Allowed transitions; anything else is a programming error. */
    public function canTransitionTo(self $target): bool
    {
        return match ($this) {
            self::QUEUED    => in_array($target, [self::SENDING, self::FAILED, self::CANCELLED], true),
            self::SENDING   => in_array($target, [self::SENT, self::FAILED], true),
            self::SENT      => self::BOUNCED === $target,
            self::FAILED    => in_array($target, [self::SENDING, self::CANCELLED], true),
            self::BOUNCED, self::CANCELLED => false,
        };
    }
}
