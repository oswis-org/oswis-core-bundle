<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait InfoMailSentTrait
{
    /**
     * Date and time of last info e-mail.
     *
     * @var DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected ?DateTime $infoMailSentDateTime = null;

    /**
     * Number of info e-mails sent.
     *
     * @var int|null
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected ?int $infoMailSentCount = 0;

    /**
     * Source/reason/author of last info e-mail (cron, manual...).
     *
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $infoMailSentSource = null;

    /**
     * Get date and time of last info e-mail.
     */
    public function getInfoMailSentDateTime(): ?DateTime
    {
        return $this->infoMailSentDateTime;
    }

    /**
     * (DO NOT USE!) Set last info e-mail date and time.
     */
    public function setInfoMailSentDateTime(?DateTime $infoMailSentDateTime): void
    {
        $this->infoMailSentDateTime = $infoMailSentDateTime;
    }

    /**
     * Get number of confirmation e-mails sent.
     */
    public function getInfoMailSentCount(): int
    {
        return $this->infoMailSentCount ?? 0;
    }

    /**
     * (DO NOT USE!) Set number of confirmations sent.
     */
    public function setInfoMailSentCount(?int $infoMailSentCount): void
    {
        $this->infoMailSentCount = $infoMailSentCount ?? 0;
    }

    /**
     * Get source/reason of last info e-mail.
     */
    public function getInfoMailSentSource(): ?string
    {
        return $this->infoMailSentSource;
    }

    /**
     * (DO NOT USE!) Set last info mail source/reason/author.
     */
    public function setInfoMailSentSource(?string $infoMailSentSource): void
    {
        $this->infoMailSentSource = $infoMailSentSource;
    }

    public function setInfoMailSent(?string $source = null): void
    {
        $this->infoMailSentCount = $this->infoMailSentCount > 0 ? $this->infoMailSentCount + 1 : 1;
        $this->infoMailSentDateTime = new DateTime();
        $this->infoMailSentSource = $source;
    }
}
