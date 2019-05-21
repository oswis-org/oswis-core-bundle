<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

trait ValueTrait
{

    /**
     * @var bool
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected $valueAllowed;

    /**
     * @var string
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     */
    protected $valueRegex;

    /**
     * @return string
     */
    final public function getValueRegex(): string
    {
        return $this->valueRegex;
    }

    /**
     * @param string $valueRegex
     */
    final public function setValueRegex(string $valueRegex): void
    {
        $this->valueRegex = $valueRegex;
    }

    /**
     * @var string
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    protected $valueLabel;


    /**
     * @return string
     */
    final public function getValueLabel(): ?string
    {
        return $this->valueLabel;
    }

    /**
     * @param string $valueLabel
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
     * @param bool $valueAllowed
     */
    final public function setValueAllowed(?bool $valueAllowed): void
    {
        $this->valueAllowed = $valueAllowed ?? false;
    }


}
