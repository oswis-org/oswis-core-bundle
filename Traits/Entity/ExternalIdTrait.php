<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait ExternalIdTrait
{
    /**
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     */
    protected ?string $externalId = null;

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): void
    {
        $this->externalId = $externalId;
    }
}
