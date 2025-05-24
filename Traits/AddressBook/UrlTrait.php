<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\AddressBook;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Trait adds phone number field.
 */
trait UrlTrait
{
    /** URL of website. */
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[Column(type: 'string', length: 255, unique: false, nullable: true)]
    #[Length(min: 4, max: 254)]
    protected ?string $url;

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }
}
