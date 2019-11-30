<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

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
    final public function getNumericValue(): int
    {
        return $this->numericValue ?? 0;
    }

    /**
     * Set numeric value.
     *
     * @param int $numericValue
     */
    final public function setNumericValue(?int $numericValue): void
    {
        $this->numericValue = $numericValue ?? 0;
    }
}
