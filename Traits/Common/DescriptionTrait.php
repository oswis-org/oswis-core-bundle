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
     * Short text description.
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
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
