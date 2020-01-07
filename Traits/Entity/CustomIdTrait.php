<?php
/**
 * @noinspection PhpUnused
 */

/**
 * @noinspection PhpUndefinedMethodInspection
 * @noinspection MethodShouldBeFinalInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds customId field.
 */
trait CustomIdTrait
{
    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255)
     */
    protected ?string $customId = null;

    public function getCustomId(): ?string
    {
        return $this->customId;
    }

    public function setCustomId(?bool $auto = true, ?string $customId = null): void
    {
        $this->customId = $auto ? $this->getAutoCustomId() : $customId;
    }

    public function getAutoCustomId(): ?string
    {
        return ''.$this->getId() ?? '';
    }
}