<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait adds textValue field.
 */
trait TextValueTrait
{
    /**
     * Text value.
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     */
    protected ?string $textValue = null;

    public function hasTextValue(): bool
    {
        return !empty($this->getTextValue());
    }

    /**
     * Get text value.
     */
    public function getTextValue(): ?string
    {
        return $this->textValue;
    }

    /**
     * Set text value.
     */
    public function setTextValue(?string $textValue): void
    {
        $this->textValue = $textValue;
    }
}
