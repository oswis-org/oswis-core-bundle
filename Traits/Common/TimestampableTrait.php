<?php

declare(strict_types=1);
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use DateTime;

use function floor;

/**
 * Trait adds createdAt and updatedAt fields.
 *
 * Trait adds fields *createdAt* and *updatedAt* and allows to access them.
 * * _**createdAt**_ contains date and time when entity was created
 * * _**updatedAt**_ contains date and time when entity was updated/changed
 */
trait TimestampableTrait
{
    /**
     * Date and time of entity creation.
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true)
     * @Gedmo\Mapping\Annotation\Timestampable(on="create")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $createdAt = null;

    /**
     * Date and time of entity update.
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime", nullable=true, options={"default" : null})
     * @Gedmo\Mapping\Annotation\Timestampable(on="update")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?DateTime $updatedAt = null;

    public function getCreatedDaysAgo(): ?int
    {
        if ($this->getCreatedAt() === null) {
            return null;
        }

        return ($ago = $this->getCreatedAt()->diff(new DateTime())->days) ? (int)floor($ago) : null;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt ?? $this->updatedAt;
    }

    public function getUpdatedDaysAgo(): ?int
    {
        if (($updatedAt = $this->getUpdatedAt()) === null) {
            return null;
        }

        return ($ago = $updatedAt->diff(new DateTime())->days) ? (int)floor($ago) : null;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt ?? $this->createdAt;
    }
}
