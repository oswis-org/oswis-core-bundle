<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use DateTimeInterface;

/**
 * Trait EntityBasicMailConfirmationTrait.
 */
trait BasicMailConfirmationTrait
{
    /**
     * Date and time of last confirmation e-mail.
     *
     * @var DateTimeInterface|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTimeInterface $eMailConfirmationDateTime = null;

    /**
     * Number of confirmation e-mails sent.
     *
     * @var int
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected int $eMailConfirmationCount = 0;

    /**
     * Source/reason/author of last e-mail confirmation (cron, manual...).
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $eMailConfirmationSource = null;

    /**
     * Get date and time of last confirmation e-mail.
     */
    public function getEMailConfirmationDateTime(): ?DateTimeInterface
    {
        return $this->eMailConfirmationDateTime;
    }

    /**
     * (DO NOT USE!) Set last confirmation date and time.
     */
    public function setEMailConfirmationDateTime(?DateTimeInterface $eMailConfirmationDateTime): void
    {
        $this->eMailConfirmationDateTime = $eMailConfirmationDateTime;
    }

    /**
     * Get number of confirmation e-mails sent.
     */
    public function getEMailConfirmationCount(): int
    {
        return $this->eMailConfirmationCount ?? 0;
    }

    /**
     * (DO NOT USE!) Set number of confirmations sent.
     *
     * @param int $eMailConfirmationCount
     */
    public function setEMailConfirmationCount(?int $eMailConfirmationCount): void
    {
        $this->eMailConfirmationCount = $eMailConfirmationCount ?? null;
    }

    /**
     * Get source/reason of last confirmation e-mail.
     */
    public function getEMailConfirmationSource(): ?string
    {
        return $this->eMailConfirmationSource;
    }

    /**
     * (DO NOT USE!) Set last confirmation source/reason/author.
     */
    public function setEMailConfirmationSource(?string $eMailConfirmationSource): void
    {
        $this->eMailConfirmationSource = $eMailConfirmationSource;
    }

    public function setMailConfirmationSend(?string $source = null): void
    {
        if (!empty($this->eMailConfirmationCount)) {
            ++$this->eMailConfirmationCount;
        } else {
            $this->eMailConfirmationCount = 1;
        }
        $this->eMailConfirmationDateTime = new DateTime();
        $this->eMailConfirmationSource = $source;
    }
}
