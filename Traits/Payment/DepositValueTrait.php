<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Payment;

use Doctrine\ORM\Mapping\Column;

trait DepositValueTrait
{
    /** Numeric value of deposit. */
    #[Column(type: 'integer', nullable: false, options: ['default' => 0])]
    protected ?int $depositValue = null;

    /** Get deposit value. */
    public function getDepositValue(): int
    {
        return $this->depositValue ?? 0;
    }

    /** Set deposit value. */
    public function setDepositValue(?int $depositValue): void
    {
        $this->depositValue = $depositValue ?? 0;
    }
}
