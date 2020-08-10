<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait adds priority field.
 */
trait PriorityTrait
{
    /**
     * Priority (numeric, positive (higher) or negative (lower)).
     *
     * @Doctrine\ORM\Mapping\Column(nullable=true, options={"default": 0})
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="exact")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter::class)
     */
    protected ?int $priority = 0;

    /**
     * Get priority.
     */
    public function getPriority(): ?int
    {
        return $this->priority;
    }

    /**
     * Set priority.
     *
     * @param int $priority
     */
    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }
}
