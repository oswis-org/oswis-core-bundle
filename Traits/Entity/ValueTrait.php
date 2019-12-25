<?php /** @noinspection MethodShouldBeFinalInspection */

/** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait ValueTrait
{
    /**
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $valueAllowed = null;

    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     */
    protected ?string $valueRegex = null;

    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
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
