<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

/**
 * Trait adds textValue field.
 */
trait TextValueTrait
{
    /** Text value. */
    #[Column(type: 'text', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(ExistsFilter::class)]
    protected ?string $textValue = null;

    public function hasTextValue(): bool
    {
        return !empty($this->getTextValue());
    }

    /** Get text value. */
    public function getTextValue(): ?string
    {
        return $this->textValue;
    }

    /** Set text value. */
    public function setTextValue(?string $textValue): void
    {
        $this->textValue = $textValue;
    }
}
