<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds textValue field.
 */
trait TextValueTrait
{
    /**
     * Text value.
     *
     * @var string|null
     *
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     */
    protected ?string $textValue = null;

    /**
     * Get text value.
     *
     * @return string
     */
    public function getTextValue(): ?string
    {
        return $this->textValue;
    }

    /**
     * Set text value.
     *
     * @param string $textValue
     */
    public function setTextValue(?string $textValue): void
    {
        $this->textValue = $textValue;
    }
}
