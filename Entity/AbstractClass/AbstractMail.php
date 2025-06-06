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
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMail;
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

    #[Column(type: 'string', nullable: true)]
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
            && $previousMail instanceof AppUserMail
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

    public function setMessageID(?string $messageID = null): void
    {
        if (!empty($this->getMessageID())) {
            return;
        }
        try {
            if (empty($messageID)) {
                $this->messageID = $this->templatedEmail?->generateMessageId();

                return;
            }
        } catch (\Symfony\Component\Mime\Exception\LogicException) {
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
}
