<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

/**
 * Trait adds description field.
 */
trait DescriptionTrait
{
    /** Short text description. */
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[Column(type: 'text', nullable: true)]
    protected ?string $description = null;

    /**
     * Get description.
     */
    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    /**
     * Set description.
     */
    public function setDescription(?string $description): void
    {
        $this->description = empty($description) ? null : $description;
    }
}
