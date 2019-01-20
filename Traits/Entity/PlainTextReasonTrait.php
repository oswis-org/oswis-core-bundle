<?php

namespace Zakjakub\OswisCoreBundle\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds plain text reason field
 */
trait PlainTextReasonTrait
{

    /**
     * Reason in plain text.
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $reason;

    /**
     * @return string|null
     */
    final public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @param string|null $reason
     */
    final public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }
}
