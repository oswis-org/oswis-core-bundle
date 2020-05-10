<?php
/**
 * @noinspection PhpUnused
 */

/**
 * @noinspection PhpUndefinedMethodInspection
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait adds customId field.
 */
trait CustomIdTrait
{
    /**
     * @Doctrine\ORM\Mapping\Column(type="string", length=255, nullable=true, unique=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="exact")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class, )
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\NumericFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter::class)
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
