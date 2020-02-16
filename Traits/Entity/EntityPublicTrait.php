<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use Zakjakub\OswisCoreBundle\Entity\Publicity;

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

    public function setFieldsFromPublicity(?Publicity $publicity = null): void
    {
        if (null !== $publicity) {
            $this->setPublicOnWeb($publicity->publicOnWeb);
            $this->setPublicOnWebRoute($publicity->publicOnWebRoute);
            $this->setPublicInIS($publicity->publicInIS);
            $this->setPublicInPortal($publicity->publicInPortal);
        }
    }

    public function setPublicOnWeb(?bool $publicOnWeb): void
    {
        $this->publicOnWeb = $publicOnWeb;
    }

    public function setPublicOnWebRoute(?bool $publicOnWebRoute): void
    {
        $this->publicOnWebRoute = $publicOnWebRoute;
    }

    public function setPublicInIS(?bool $publicInIS): void
    {
        $this->publicInIS = $publicInIS;
    }

    public function setPublicInPortal(?bool $publicInPortal): void
    {
        $this->publicInPortal = $publicInPortal;
    }

    public function getPublicity(): Publicity
    {
        return new Publicity($this->isPublicOnWeb(), $this->isPublicOnWebRoute(), $this->isPublicInIS(), $this->isPublicInPortal());
    }

    public function isPublicOnWeb(): bool
    {
        return $this->publicOnWeb ?? false;
    }

    public function isPublicOnWebRoute(): bool
    {
        return $this->publicOnWebRoute ?? false;
    }

    public function isPublicInIS(): bool
    {
        return $this->publicInIS ?? false;
    }

    public function isPublicInPortal(): bool
    {
        return $this->publicInPortal ?? false;
    }


}
