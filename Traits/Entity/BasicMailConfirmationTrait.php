<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;
use function date_create;

/**
 * Trait EntityBasicMailConfirmationTrait.
 */
trait BasicMailConfirmationTrait
{
    /**
     * Date and time of last confirmation e-mail.
     *
     * @var DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $eMailConfirmationDateTime = null;

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
    final public function getEMailConfirmationDateTime(): ?DateTime
    {
        return $this->eMailConfirmationDateTime;
    }

    /**
     * (DO NOT USE!) Set last confirmation date and time.
     */
    final public function setEMailConfirmationDateTime(?DateTime $eMailConfirmationDateTime): void
    {
        $this->eMailConfirmationDateTime = $eMailConfirmationDateTime;
    }

    /**
     * Get number of confirmation e-mails sent.
     */
    final public function getEMailConfirmationCount(): int
    {
        return $this->eMailConfirmationCount ?? 0;
    }

    /**
     * (DO NOT USE!) Set number of confirmations sent.
     *
     * @param int $eMailConfirmationCount
     */
    final public function setEMailConfirmationCount(?int $eMailConfirmationCount): void
    {
        $this->eMailConfirmationCount = $eMailConfirmationCount ?? null;
    }

    /**
     * Get source/reason of last confirmation e-mail.
     */
    final public function getEMailConfirmationSource(): ?string
    {
        return $this->eMailConfirmationSource;
    }

    /**
     * (DO NOT USE!) Set last confirmation source/reason/author.
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
        $this->eMailConfirmationDateTime = date_create();
        $this->eMailConfirmationSource = $source;
    }
}
