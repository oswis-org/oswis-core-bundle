<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds priority field.
 */
trait PriorityTrait
{
    /**
     * Priority (numeric).
     *
     * @var int|null
     *
     * @Doctrine\ORM\Mapping\Column(nullable=true, options={"default": 0})
     */
    protected ?int $priority = 0;

    /**
     * Get priority.
     */
    public function getPriority(): int
    {
        return $this->priority ?? 0;
    }

    /**
     * Set priority.
     *
     * @param int $priority
     */
    public function setPriority(?int $priority): void
    {
        $this->priority = $priority ?? 0;
    }
}
