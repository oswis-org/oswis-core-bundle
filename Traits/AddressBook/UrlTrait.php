<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\AddressBook;

/**
 * Trait adds phone number field.
 */
trait UrlTrait
{
    /**
     * URL of website.
     * @Doctrine\ORM\Mapping\Column(type="string", unique=false, length=255, nullable=true)
     * @Symfony\Component\Validator\Constraints\Length(
     *      min = 4,
     *      max = 254
     * )
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="ipartial")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
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
