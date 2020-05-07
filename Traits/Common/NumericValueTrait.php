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
     * Numeric value.
     *
     * @var int|null
     *
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=false, options={"default": 0})
     */
    protected ?int $numericValue = null;

    /**
     * Get numeric value.
     */
    public function getNumericValue(): int
    {
        return $this->numericValue ?? 0;
    }

    /**
     * Set numeric value.
     *
     * @param int $numericValue
     */
    public function setNumericValue(?int $numericValue): void
    {
        $this->numericValue = $numericValue ?? 0;
    }
}
