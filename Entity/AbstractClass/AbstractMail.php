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
        if (($previousMail = $sortedPastMails->first() ?: null)
            && $previousMail instanceof self
            && !empty($previousMail->getMessageID())) {
            $headers->addIdHeader('In-Reply-To', $previousMail->getMessageID());
        }
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
        $rawSubject = trim($subject ?? '');
        $normalizedSubject = preg_replace('/^(re|fwd?|fw|odp|odpoved|odpověď)\s*:\s*/iu', '', $rawSubject);
        if (!is_string($normalizedSubject) || '' === trim($normalizedSubject)) {
            $normalizedSubject = '(no subject)';
        }
        $normalizedSubject = mb_strtolower(trim($normalizedSubject));
        $normalizedEmail = mb_strtolower(trim($email));

        return sha1($normalizedSubject.'|'.$normalizedEmail);
    }
}
