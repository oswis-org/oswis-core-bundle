<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait DepositValueTrait
{
    /**
     * Numeric value of deposit.
     *
     * @var int|null
     *
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=false, options={"default": 0})
     */
    protected ?int $depositValue = null;

    /**
     * Get deposit value.
     */
    final public function getDepositValue(): int
    {
        return $this->depositValue ?? 0;
    }

    /**
     * Set deposit value.
     */
    final public function setDepositValue(int $depositValue): void
    {
        $this->depositValue = $depositValue ?? 0;
    }
}
