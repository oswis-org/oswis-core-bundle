<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

/**
 * Trait adds floatValue field.
 */
trait FloatValueTrait
{
    /** Float numeric value. */
    #[Column(type: 'float', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    protected ?float $floatValue = null;

    public function getFloatValue(): ?float
    {
        return $this->floatValue;
    }

    public function setFloatValue(?float $floatValue): void
    {
        $this->floatValue = $floatValue;
    }
}
