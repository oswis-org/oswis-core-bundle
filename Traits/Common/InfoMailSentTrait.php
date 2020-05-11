<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;

trait InfoMailSentTrait
{
    /**
     * Date and time of last sent info e-mail.
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $infoMailSentDateTime = null;

    /**
     * Number of info e-mails sent.
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="exact")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?int $infoMailSentCount = 0;

    /**
     * Source/reason/author of last info e-mail (cron, manual...).
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
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
