<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use DateTime;
use Doctrine\ORM\Mapping\Column;
use Exception;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

/**
 * Trait adds dateTime field.
 */
trait DateTimeTrait
{
    /** Date and time. */
    #[Column(type: 'datetime', nullable: true, options: ['default' => null])]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(DateFilter::class)]
    protected ?DateTime $dateTime = null;

    public function getDaysAgo(?bool $decimal = false): ?float
    {
        try {
            if (null !== $this->getDateTime()) {
                $ago = $this->getDateTime()->diff(new DateTime());

                return (float)($decimal ? $ago->days : $ago->d);
            }

            return null;
        } catch (Exception) {
            return null;
        }
    }

    public function getDateTime(): ?DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(?DateTime $dateTime = null): void
    {
        $this->dateTime = $dateTime ? clone $dateTime : null;
    }
}
