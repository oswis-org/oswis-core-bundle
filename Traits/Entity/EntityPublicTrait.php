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
     *
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $publicOnWeb = null;

    /**
     * Entity is visible on automatically generated route (only of it's visible on website).
     *
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $publicOnWebRoute = null;

    /**
     * Entity is visible in IS.
     *
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $publicInIS = null;

    /**
     * Entity is visible in portal.
     *
     * @var bool|null
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $publicInPortal = null;

    public function isPublicOnWeb(): bool
    {
        return $this->publicOnWeb ?? false;
    }

    /**
     * @param bool $publicOnWeb
     */
    public function setPublicOnWeb(?bool $publicOnWeb): void
    {
        $this->publicOnWeb = $publicOnWeb;
    }

    public function isPublicOnWebRoute(): bool
    {
        return $this->publicOnWebRoute ?? false;
    }

    /**
     * @param bool $publicOnWebRoute
     */
    public function setPublicOnWebRoute(?bool $publicOnWebRoute): void
    {
        $this->publicOnWebRoute = $publicOnWebRoute;
    }

    public function isPublicInIS(): bool
    {
        return $this->publicInIS ?? false;
    }

    /**
     * @param bool $publicInIS
     */
    public function setPublicInIS(?bool $publicInIS): void
    {
        $this->publicInIS = $publicInIS;
    }

    public function isPublicInPortal(): bool
    {
        return $this->publicInPortal ?? false;
    }

    /**
     * @param bool $publicInPortal
     */
    public function setPublicInPortal(?bool $publicInPortal): void
    {
        $this->publicInPortal = $publicInPortal;
    }
}
