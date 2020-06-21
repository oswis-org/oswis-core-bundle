<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
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

    /**
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $sent = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $recipientName = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $subject = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $messageID = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $statusMessage = null;

    /**
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $address = null;

    protected ?TemplatedEmail $templatedEmail = null;

    /**
     * @throws InvalidTypeException
     */
    public function __construct(string $subject, string $address, ?string $type = null, ?string $recipientName = null, ?string $messageID = null)
    {
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

    /**
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @return string|null
     */
    public function getMessageID(): ?string
    {
        return $this->messageID;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
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
        if (!empty($this->messageID)) {
            throw new OswisException('Nelze znovu odeslat stejný e-mail.');
        }
        $this->templatedEmail = new TemplatedEmail();
        $this->templatedEmail->subject($this->subject);
        try {
            $this->templatedEmail->to(new Address($this->address ?? '', $this->recipientName ?? ''));
        } catch (LogicException $e) {
            $this->templatedEmail->to($this->address ?? '');
        }
        try {
            $this->messageID = $this->templatedEmail->generateMessageId();
        } catch (LogicException $e) {
        }

        return $this->templatedEmail;
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
