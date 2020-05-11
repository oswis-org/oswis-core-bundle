<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait adds "active" field.
 */
trait ActiveTrait
{
    /**
     * Active.
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?bool $active = null;

    public function isActive(): bool
    {
        return $this->getActive();
    }

    /**
     * Get value of "active" field.
     */
    public function getActive(): bool
    {
        return $this->active ?? false;
    }

    /**
     * Set value of "active" field.
     *
     * @param bool $active
     */
    public function setActive(?bool $active = false): void
    {
        $this->active = $active ?? false;
    }
}
