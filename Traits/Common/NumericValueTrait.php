<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait adds numericValue field.
 */
trait NumericValueTrait
{
    /**
     * Numeric value (positive or negative).
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="exact")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?int $numericValue = null;

    /**
     * Get numeric value.
     */
    public function getNumericValue(): ?int
    {
        return $this->numericValue;
    }

    /**
     * Set numeric value.
     */
    public function setNumericValue(?int $numericValue): void
    {
        $this->numericValue = $numericValue;
    }
}
