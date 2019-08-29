<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait InfoMailSentTrait
{

    /**
     * Date and time of last info e-mail.
     * @var DateTime|null
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     */
    protected $infoMailSentDateTime;

    /**
     * Number of info e-mails sent.
     * @var int
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     */
    protected $infoMailSentCount;

    /**
     * Source/reason/author of last info e-mail (cron, manual...).
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected $infoMailSentSource;

    /**
     * Get date and time of last info e-mail.
     * @return DateTime|null
     */
    final public function getInfoMailSentDateTime(): ?DateTime
    {
        return $this->infoMailSentDateTime;
    }

    /**
     * (DO NOT USE!) Set last info e-mail date and time.
     *
     * @param DateTime|null $infoMailSentDateTime
     */
    final public function setInfoMailSentDateTime(?DateTime $infoMailSentDateTime): void
    {
        $this->infoMailSentDateTime = $infoMailSentDateTime;
    }

    /**
     * Get number of confirmation e-mails sent.
     * @return int
     */
    final public function getInfoMailSentCount(): int
    {
        return $this->infoMailSentCount ?? 0;
    }

    /**
     * (DO NOT USE!) Set number of confirmations sent.
     *
     * @param int|null $infoMailSentCount
     */
    final public function setInfoMailSentCount(?int $infoMailSentCount): void
    {
        $this->infoMailSentCount = $infoMailSentCount ?? null;
    }

    /**
     * Get source/reason of last info e-mail.
     * @return string|null
     */
    final public function getInfoMailSentSource(): ?string
    {
        return $this->infoMailSentSource;
    }

    /**
     * (DO NOT USE!) Set last info mail source/reason/author.
     *
     * @param string|null $infoMailSentSource
     */
    final public function setInfoMailSentSource(?string $infoMailSentSource): void
    {
        $this->infoMailSentSource = $infoMailSentSource;
    }

    final public function setInfoMailSent(?string $source = null): void
    {
        if ($this->infoMailSentCount && $this->infoMailSentCount > 0) {
            ++$this->infoMailSentCount;
        } else {
            $this->infoMailSentCount = 1;
        }
        $this->infoMailSentDateTime = new DateTime();
        $this->infoMailSentSource = $source;
    }
}
