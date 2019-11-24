<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait ValueTrait
{

    /**
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $valueAllowed;

    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     */
    protected ?string $valueRegex;

    /**
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected ?string $valueLabel;

    /**
     * @return string|null
     */
    final public function getValueRegex(): ?string
    {
        return $this->valueRegex;
    }

    /**
     * @param string|null $valueRegex
     */
    final public function setValueRegex(?string $valueRegex): void
    {
        $this->valueRegex = $valueRegex;
    }

    /**
     * @return string|null
     */
    final public function getValueLabel(): ?string
    {
        return $this->valueLabel;
    }

    /**
     * @param string|null $valueLabel
     */
    final public function setValueLabel(?string $valueLabel): void
    {
        $this->valueLabel = $valueLabel;
    }

    /**
     * @return bool
     */
    final public function isValueAllowed(): bool
    {
        return $this->valueAllowed ?? false;
    }

    /**
     * @param bool|null $valueAllowed
     */
    final public function setValueAllowed(?bool $valueAllowed): void
    {
        $this->valueAllowed = $valueAllowed ?? false;
    }
}
