<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

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
