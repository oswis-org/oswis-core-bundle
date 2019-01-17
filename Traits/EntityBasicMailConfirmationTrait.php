<?php

namespace Zakjakub\OswisCoreBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait EntityBasicMailConfirmationTrait
 * @package OswisResources
 */
trait EntityBasicMailConfirmationTrait
{

    /**
     * Date and time of last confirmation e-mail.
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $eMailConfirmationDateTime;

    /**
     * Number of confirmation e-mails sent.
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $eMailConfirmationCount;

    /**
     * Source/reason/author of last e-mail confirmation (cron, manual...).
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $eMailConfirmationSource;

    /**
     * Get date and time of last confirmation e-mail.
     * @return \DateTime|null
     */
    final public function getEMailConfirmationDateTime(): ?\DateTime
    {
        return $this->eMailConfirmationDateTime;
    }

    /**
     * (DO NOT USE!) Set last confirmation date and time.
     *
     * @param \DateTime|null $eMailConfirmationDateTime
     */
    final public function setEMailConfirmationDateTime(?\DateTime $eMailConfirmationDateTime): void
    {
        $this->eMailConfirmationDateTime = $eMailConfirmationDateTime;
    }

    /**
     * Get number of confirmation e-mails sent.
     * @return int
     */
    final public function getEMailConfirmationCount(): int
    {
        return $this->eMailConfirmationCount;
    }

    /**
     * (DO NOT USE!) Set number of confirmations sent.
     *
     * @param int $eMailConfirmationCount
     */
    final public function setEMailConfirmationCount(int $eMailConfirmationCount): void
    {
        $this->eMailConfirmationCount = $eMailConfirmationCount;
    }

    /**
     * Get source/reason of last confirmation e-mail.
     * @return string|null
     */
    final public function getEMailConfirmationSource(): ?string
    {
        return $this->eMailConfirmationSource;
    }

    /**
     * (DO NOT USE!) Set last confirmation source/reason/author.
     *
     * @param string|null $eMailConfirmationSource
     */
    final public function setEMailConfirmationSource(?string $eMailConfirmationSource): void
    {
        $this->eMailConfirmationSource = $eMailConfirmationSource;
    }

    final public function setMailConfirmationSend(?string $source = null): void
    {
        if ($this->eMailConfirmationCount && $this->eMailConfirmationCount > 0) {
            ++$this->eMailConfirmationCount;
        } else {
            $this->eMailConfirmationCount = 1;
        }
        $this->eMailConfirmationDateTime = \date_create();
        $this->eMailConfirmationSource = $source;
    }
}
