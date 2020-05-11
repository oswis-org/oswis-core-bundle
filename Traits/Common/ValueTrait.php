<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

trait ValueTrait
{
    /**
     * Form settings - is value allowed?
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?bool $valueAllowed = null;

    /**
     * Form settings - regex for value.
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $valueRegex = null;

    /**
     * Form settings - value label.
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?string $valueLabel = null;

    public function getValueRegex(): ?string
    {
        return $this->valueRegex;
    }

    public function setValueRegex(?string $valueRegex): void
    {
        $this->valueRegex = $valueRegex;
    }

    public function getValueLabel(): ?string
    {
        return $this->valueLabel;
    }

    public function setValueLabel(?string $valueLabel): void
    {
        $this->valueLabel = $valueLabel;
    }

    public function isValueAllowed(): bool
    {
        return $this->valueAllowed ?? false;
    }

    public function setValueAllowed(?bool $valueAllowed): void
    {
        $this->valueAllowed = $valueAllowed ?? false;
    }
}
