<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use LogicException;
use OswisOrg\OswisCoreBundle\Enum\Mail\MailDeliveryStatus;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Interfaces\Common\BasicInterface;
use OswisOrg\OswisCoreBundle\Traits\Common\BasicTrait;
use OswisOrg\OswisCoreBundle\Traits\Common\TypeTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;

/**
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractMail implements BasicInterface
{
    use BasicTrait;
    use TypeTrait;

    #[Column(type: 'datetime', nullable: true)]
    protected ?DateTime $sent = null;

    /**
     * Where this delivery stands. `sent` stays the timestamp of SMTP acceptance (every existing
     * query `sent IS NOT NULL` keeps its meaning); the status additionally distinguishes
     * "sending / uncertain", "bounced" and "cancelled" — see {@see MailDeliveryStatus}.
     */
    #[Column(type: 'string', length: 16, enumType: MailDeliveryStatus::class, options: ['default' => 'queued'])]
    protected MailDeliveryStatus $status = MailDeliveryStatus::QUEUED;

    /** How many times this delivery was handed to SMTP (an automatic retry reuses the record). */
    #[Column(type: 'integer', options: ['default' => 0])]
    protected int $attemptCount = 0;

    /**
     * Idempotency key of an AUTOMATIC send, unique per table — the database, not code discipline,
     * is what prevents a second delivery of the same thing (cron × button × parallel run). Manual
     * sends and resends leave it NULL, and NULL repeats freely in a unique index.
     *
     * Shape: `purpose:id[:id…]`, built by {@see \OswisOrg\OswisCoreBundle\Mail\Delivery\DeliveryKey}.
     */
    #[Column(type: 'string', length: 191, nullable: true)]
    protected ?string $deliveryKey = null;

    #[Column(type: 'string', nullable: true)]
    protected ?string $recipientName = null;

    #[Column(type: 'string', nullable: true)]
    protected ?string $subject = null;

    #[Column(type: 'string', nullable: true)]
    protected ?string $messageID = null;

    // type=text (LONGTEXT in MySQL) instead of VARCHAR(255). Mailer error
    // strings from Symfony/Mailer can easily exceed 255 chars — caught the
    // 2026 launch (1406 Data too long on a perfectly normal mail try).
    #[Column(type: 'text', nullable: true)]
    protected ?string $statusMessage = null;

    #[Column(type: 'string', nullable: true)]
    protected ?string $address = null;

    // Rendered body of the sent mail, captured at send-time by MailService so the
    // admin communication timeline can show what was actually delivered. type=text
    // (LONGTEXT in MySQL) — full HTML mails far exceed VARCHAR. Old rows stay NULL
    // (no backfill) and the timeline falls back to "tělo není k dispozici".
    #[Column(type: 'text', nullable: true)]
    protected ?string $bodyHtml = null;

    #[Column(type: 'text', nullable: true)]
    protected ?string $body = null;

    protected ?TemplatedEmail $templatedEmail = null;

    /**
     * @throws InvalidTypeException
     */
    public function __construct(
        ?string $subject = null,
        ?string $address = null,
        ?string $type = null,
        ?string $recipientName = null,
        ?string $messageID = null
    ) {
        $this->subject = $subject;
        $this->address = $address;
        $this->recipientName = $recipientName;
        $this->messageID = $messageID;
        $this->setType($type);
    }

    public static function getAllowedTypesDefault(): array
    {
        return [''];
    }

    public static function getAllowedTypesCustom(): array
    {
        return [];
    }

    public static function checkType(?string $typeName): bool
    {
        return true;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function isSent(): bool
    {
        return (bool)$this->getSent();
    }

    public function getStatus(): MailDeliveryStatus
    {
        return $this->status;
    }

    public function getAttemptCount(): int
    {
        return $this->attemptCount;
    }

    public function getDeliveryKey(): ?string
    {
        return $this->deliveryKey;
    }

    /**
     * Key of an automatic send; may be set only before the first attempt, because it is what
     * a concurrent sender collides with. Manual sends keep it NULL.
     */
    public function setDeliveryKey(?string $deliveryKey): void
    {
        if (0 < $this->attemptCount) {
            throw new LogicException('Klíč jedinečnosti nelze změnit po pokusu o odeslání.');
        }
        $this->deliveryKey = $deliveryKey;
    }

    /** About to hand the message to SMTP — recorded (and committed) BEFORE the send. */
    public function markSending(): void
    {
        $this->changeStatus(MailDeliveryStatus::SENDING);
        $this->attemptCount++;
        $this->statusMessage = null;
    }

    /** SMTP accepted the message. */
    public function markSent(?DateTime $sentAt = null): void
    {
        $this->changeStatus(MailDeliveryStatus::SENT);
        $this->setSent($sentAt ?? new DateTime());
    }

    /** SMTP refused the message, or it could not be rendered — an automatic retry is allowed. */
    public function markFailed(string $reason): void
    {
        $this->changeStatus(MailDeliveryStatus::FAILED);
        $this->setStatusMessage($reason);
    }

    /** The message was accepted and came back later (DSN from the recipient's server). */
    public function markBounced(string $reason): void
    {
        $this->changeStatus(MailDeliveryStatus::BOUNCED);
        $this->setStatusMessage($reason);
    }

    /** A queued (or failed) delivery the team called off; nothing will be sent. */
    public function markCancelled(?string $reason = null): void
    {
        $this->changeStatus(MailDeliveryStatus::CANCELLED);
        if (null !== $reason) {
            $this->setStatusMessage($reason);
        }
    }

    /**
     * Status of an unfinished delivery that nobody is working on any more.
     *
     * A record left in SENDING means the process died between handing the message to SMTP and
     * writing the result: the message may or may not have gone out. Such a delivery is never
     * retried automatically — the team checks the archive mailbox and decides.
     */
    public function isUncertain(?DateTime $now = null, int $staleMinutes = 10): bool
    {
        if (MailDeliveryStatus::SENDING !== $this->status) {
            return false;
        }
        $updated = $this->getUpdatedAt() ?? $this->getCreatedAt();

        return null === $updated
               || $updated->getTimestamp() < ($now ?? new DateTime())->getTimestamp() - $staleMinutes * 60;
    }

    private function changeStatus(MailDeliveryStatus $target): void
    {
        if (!$this->status->canTransitionTo($target)) {
            throw new LogicException(
                sprintf('E-mail nelze převést ze stavu „%s" do „%s".', $this->status->value, $target->value),
            );
        }
        $this->status = $target;
    }

    public function getSent(): ?DateTime
    {
        return $this->sent;
    }

    public function setSent(?DateTime $sent): void
    {
        $this->sent = $sent;
        $this->setMessageID();
    }

    /**
     * @param Collection<AbstractMail> $sortedPastMails
     *
     * @return void
     */
    public function setPastMails(Collection $sortedPastMails): void
    {
        try {
            $templatedMail = $this->getTemplatedEmail();
        } catch (OswisException) {
            return;
        }
        $headers = $templatedMail->getHeaders();
        // `In-Reply-To` se ZÁMĚRNĚ nenastavuje (rozhodnutí 2026-07-31): mířilo na poslední
        // JAKOUKOLI předchozí zprávu bez ohledu na druh a ročník, takže se potvrzení platby
        // zavěsilo třeba pod „Ověření přihlášky“ a účastníci pak zprávy nemohli najít.
        //
        // ⚠️ Vlákno tím ale nezanikne: Gmail od 2019 řadí do konverzace podle SHODY V `References`
        // (a bez ní nevlákní ani zprávy se stejným předmětem), takže samotné odebrání `In-Reply-To`
        // se u většiny účastníků neprojeví. Kdyby si na slepené konverzace stěžovali dál, řešení je
        // ZÚŽIT rozsah právě zde — filtrovat `$sortedPastMails` na tentýž ročník a druh zprávy.
        $ids = $sortedPastMails->filter(fn(mixed $mail) => $mail instanceof AbstractMail
                                                           && !empty($mail->getMessageID()))->map(fn(mixed $mail
        ) => $mail instanceof AbstractMail ? $mail->getMessageID() : null);
        if ($ids->count() > 0) {
            $headers->addIdHeader('References', $ids->toArray());
        }
    }

    /**
     * @return TemplatedEmail
     * @throws OswisException
     */
    public function getTemplatedEmail(): TemplatedEmail
    {
        if (null !== $this->templatedEmail) {
            return $this->templatedEmail;
        }
        if (!empty($this->sent)) {
            throw new OswisException('Nelze znovu odeslat stejný e-mail.');
        }
        $this->templatedEmail = new TemplatedEmail();
        $this->templatedEmail->subject(''.$this->subject);
        try {
            $this->templatedEmail->to(new Address($this->address ?? '', $this->recipientName ?? ''));
        } catch (LogicException) {
            $this->templatedEmail->to($this->address ?? '');
        }
        $this->setMessageID();

        return $this->templatedEmail ?? throw new OswisException();
    }

    public function getMessageID(): ?string
    {
        return $this->messageID;
    }

    /**
     * Označí mail jako ručně skládaný (admin compose, ne automatický).
     *
     * MailerSubscriber detekuje hlavičku X-OSWIS-Manual a nastaví
     * Auto-Submitted: no (RFC 3834) místo auto-generated. Hlavičku samotnou
     * pak ze zprávy odstraní, takže se na drátě neukáže — je to interní
     * marker, nikoli veřejné metadata.
     */
    public function markAsManual(): void
    {
        try {
            $templatedEmail = $this->getTemplatedEmail();
        } catch (OswisException) {
            return;
        }
        $headers = $templatedEmail->getHeaders();
        if (!$headers->has('X-OSWIS-Manual')) {
            $headers->addTextHeader('X-OSWIS-Manual', '1');
        }
    }

    public function setMessageID(?string $messageID = null): void
    {
        if (!empty($this->getMessageID())) {
            return;
        }
        // Symfony\Mime\Message::generateMessageId() requires a From / Sender
        // header that MailerSubscriber only attaches at send-time → it threw
        // LogicException here, the previous code caught it and ended up
        // storing $this->messageID = null. The DB column then stayed NULL
        // across 20 000+ sent mails → no threading possible. Generate the
        // ID ourselves in the standard Symfony format, bind it onto the
        // outgoing Email's headers so the wire copy matches the DB row.
        if (empty($messageID)) {
            $messageID = bin2hex(random_bytes(16)).'@oswis.seznamovakup.cz';
        }
        if (null !== $this->templatedEmail) {
            $headers = $this->templatedEmail->getHeaders();
            if (!$headers->has('Message-ID')) {
                $headers->addIdHeader('Message-ID', $messageID);
            }
        }
        $this->messageID = $messageID;
    }

    public function getRecipientName(): ?string
    {
        return $this->recipientName;
    }

    public function getStatusMessage(): ?string
    {
        return $this->statusMessage;
    }

    public function setStatusMessage(?string $message): void
    {
        $this->statusMessage = $message;
    }

    public function getBodyHtml(): ?string
    {
        return $this->bodyHtml;
    }

    public function setBodyHtml(?string $bodyHtml): void
    {
        $this->bodyHtml = $bodyHtml;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): void
    {
        $this->body = $body;
    }

    /**
     * Compute deterministic thread key from subject + recipient email.
     *
     * Used to group mail / phone / chat entries belonging to one conversation
     * even when In-Reply-To headers are missing or broken. Stable across re-sends
     * because we strip Re:/Fwd: prefix and lowercase.
     */
    public static function computeThreadKey(?string $subject, ?string $email): ?string
    {
        if (null === $email || '' === trim($email)) {
            return null;
        }
        $normalizedEmail = mb_strtolower(trim($email));

        return sha1(self::normalizeSubject($subject).'|'.$normalizedEmail);
    }

    /**
     * Předmět zbavený prefixů odpovědi a přeposlání, malými písmeny.
     *
     * Slouží ke dvěma věcem: je to půlka `computeThreadKey()` a zároveň jediné vodítko,
     * podle kterého se v přehledu komunikace pozná, že jde o TÉHOŽ vlákno.
     *
     * ⚠️ Na rozdíl od `computeThreadKey()` sem NEVSTUPUJE e-mailová adresa — a schválně:
     * klíč vlákna je odvozený od odesílatele, takže naše odpověď a odpověď účastníka
     * dostanou RŮZNÝ klíč, přestože jde o jednu konverzaci. Pro seskupení v přehledu
     * je proto potřeba tahle, adresy si nevšímající, varianta.
     */
    public static function normalizeSubject(?string $subject): string
    {
        $bezPrefixu = preg_replace('/^((re|fwd?|fw|odp|odpoved|odpověď)\s*:\s*)+/iu', '', trim($subject ?? ''));
        if (!is_string($bezPrefixu) || '' === trim($bezPrefixu)) {
            return '(no subject)';
        }
        $zhustene = preg_replace('/\s+/u', ' ', $bezPrefixu);

        return mb_strtolower(trim(is_string($zhustene) ? $zhustene : $bezPrefixu));
    }

    /**
     * Čitelný text z HTML těla e-mailu — pro náhled v časové ose.
     *
     * PROČ to je potřeba: klienti jako Gmail nebo Outlook na webu posílají zprávu
     * ČASTO JEN v HTML, textová část je prázdná. Náhled odvozený pouze z `body`
     * pak u takové zprávy nevrátí nic a v přehledu komunikace vypadá celá odchozí
     * půlka konverzace jako prázdné řádky. (Naměřeno 27. 8. 2026 na klonu:
     * 96 z 372 odchozích zpráv mělo prázdné `body` a plné `body_html`.)
     *
     * Neřeší se tu bezpečnost — výstup je čistý text bez značek, takže se dá
     * vypsat i tam, kde HTML nechceme.
     */
    public static function plainTextFromHtml(?string $html): ?string
    {
        if (null === $html || '' === trim($html)) {
            return null;
        }
        // Skripty a styly nesou obsah, který by jinak v textu skončil jako smetí.
        $bezSkriptu = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', ' ', $html);
        $text = is_string($bezSkriptu) ? $bezSkriptu : $html;
        // Konce bloků na mezeru, ať se slova ze sousedních značek neslepí.
        $sZlomy = preg_replace('#<(br|/p|/div|/tr|/li|/h[1-6])\s*/?>#i', ' ', $text);
        $text = is_string($sZlomy) ? $sZlomy : $text;
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Nezlomitelná mezera a BOM se v mailech objevují běžně a v náhledu ruší.
        $text = str_replace(["\u{A0}", "\u{FEFF}"], ' ', $text);
        $zhustene = preg_replace('/\s+/u', ' ', $text);
        $text = trim(is_string($zhustene) ? $zhustene : $text);

        return '' === $text ? null : $text;
    }
}
