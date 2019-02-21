<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds phone number field.
 */
trait UrlTrait
{

    /**
     * URL of website.
     * @var string|null
     * @Doctrine\ORM\Mapping\Column(type="string", unique=true, length=255, nullable=true)
     * @Symfony\Component\Validator\Constraints\Length(
     *      min = 4,
     *      max = 254
     * )
     */
    protected $url;

    /**
     * Get url.
     * @return string
     */
    final public function getUrl(): string
    {
        return $this->url ?? '';
    }

    /**
     * Set url.
     *
     * @param null|string $url
     */
    final public function setUrl(?string $url): void
    {
        $this->url = $url;
    }
}