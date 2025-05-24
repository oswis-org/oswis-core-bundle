<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Gedmo\Mapping\Annotation\Timestampable;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
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
    /** Date and time of entity creation. */
    #[Column(type: 'datetime', nullable: true)]
    #[Timestampable(on: 'create')]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(DateFilter::class)]
    #[ApiFilter(OrderFilter::class)]
    protected ?DateTime $createdAt = null;

    /** Date and time of entity update. */
    #[Column(type: 'datetime', nullable: true, options: ['default' => null])]
    #[Timestampable(on: 'update')]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(DateFilter::class)]
    #[ApiFilter(OrderFilter::class)]
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
