<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds textValue field
 */
trait TextValueTrait
{

    /**
     * Text value.
     *
     * @var string|null
     *
     * @Doctrine\ORM\Mapping\Column(type="string", nullable=true)
     */
    private $textValue;

    /**
     * Get text value.
     *
     * @return string
     */
    final public function getTextValue(): ?string
    {
        return $this->textValue;
    }

    /**
     * Set text value.
     *
     * @param string $textValue
     */
    final public function setTextValue(?string $textValue): void
    {
        $this->textValue = $textValue;
    }
}
