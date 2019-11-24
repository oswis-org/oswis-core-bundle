<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds plain text reason field
 */
trait PlainTextReasonTrait
{

    /**
     * Reason in plain text.
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $reason;

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
