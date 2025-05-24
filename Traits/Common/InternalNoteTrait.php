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
 * Trait adds internalNote field.
 *
 * Trait adds field *note* that contains some note for entity + getter and setter.
 */
trait InternalNoteTrait
{
    /** Internal (non-public) note. */
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[Column(type: 'text', nullable: true)]
    protected ?string $internalNote = null;

    /**
     * Get internal note.
     */
    public function getInternalNote(): ?string
    {
        return $this->internalNote;
    }

    /**
     * Set internal note.
     */
    public function setInternalNote(?string $internalNote): void
    {
        $this->internalNote = $internalNote;
    }
}
