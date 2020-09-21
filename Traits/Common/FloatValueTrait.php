<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait adds floatValue field.
 */
trait FloatValueTrait
{
    /**
     * Float numeric value.
     * @Doctrine\ORM\Mapping\Column(type="float", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="exact")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?int $floatValue = null;

    public function getFloatValue(): ?float
    {
        return $this->floatValue;
    }

    public function setFloatValue(?float $floatValue): void
    {
        $this->floatValue = $floatValue;
    }
}
