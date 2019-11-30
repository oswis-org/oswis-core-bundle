<?php /** @noinspection PhpUnused */

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

    final public function getValueRegex(): ?string
    {
        return $this->valueRegex;
    }

    final public function setValueRegex(?string $valueRegex): void
    {
        $this->valueRegex = $valueRegex;
    }

    final public function getValueLabel(): ?string
    {
        return $this->valueLabel;
    }

    final public function setValueLabel(?string $valueLabel): void
    {
        $this->valueLabel = $valueLabel;
    }

    final public function isValueAllowed(): bool
    {
        return $this->valueAllowed ?? false;
    }

    final public function setValueAllowed(?bool $valueAllowed): void
    {
        $this->valueAllowed = $valueAllowed ?? false;
    }
}
