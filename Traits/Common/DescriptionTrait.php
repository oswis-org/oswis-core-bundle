<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait adds description field.
 */
trait DescriptionTrait
{
    /**
     * Description.
     *
     * @var string|null
     *
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     */
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
