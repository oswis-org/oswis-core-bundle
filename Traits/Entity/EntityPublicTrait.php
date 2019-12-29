<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds fields that describing visibility of entity.
 */
trait EntityPublicTrait
{
    /**
     * Entity is visible on website.
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $publicOnWeb = null;

    /**
     * Entity is visible on automatically generated route.
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $publicOnWebRoute = null;

    /**
     * Entity is visible in IS (is used somewhere?).
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $publicInIS = null;

    /**
     * Entity is visible in portal (is used somewhere?).
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $publicInPortal = null;

    public function isPublicOnWeb(): bool
    {
        return $this->publicOnWeb ?? false;
    }

    public function setPublicOnWeb(?bool $publicOnWeb): void
    {
        $this->publicOnWeb = $publicOnWeb;
    }

    public function isPublicOnWebRoute(): bool
    {
        return $this->publicOnWebRoute ?? false;
    }

    public function setPublicOnWebRoute(?bool $publicOnWebRoute): void
    {
        $this->publicOnWebRoute = $publicOnWebRoute;
    }

    public function isPublicInIS(): bool
    {
        return $this->publicInIS ?? false;
    }

    public function setPublicInIS(?bool $publicInIS): void
    {
        $this->publicInIS = $publicInIS;
    }

    public function isPublicInPortal(): bool
    {
        return $this->publicInPortal ?? false;
    }

    public function setPublicInPortal(?bool $publicInPortal): void
    {
        $this->publicInPortal = $publicInPortal;
    }
}
