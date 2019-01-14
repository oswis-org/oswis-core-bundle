<?php

namespace Zakjakub\OswisResourcesBundle\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait adds plain text reason field
 */
trait EntityPlainTextReasonTrait
{

    /**
     * Reason form this special period.
     * @var string
     * @ORM\Column(type="string")
     */
    protected $reason;

    /**
     * @return string
     */
    final public function getReason(): string
    {
        return $this->reason ?? '';
    }

    /**
     * @param mixed $reason
     */
    final public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }
}
