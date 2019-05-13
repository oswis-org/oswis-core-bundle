<?php

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

/**
 * Trait adds fields that describing visibility of entity.
 */
trait EntityPublicTrait
{

    /**
     * Entity is visible on website.
     * @var bool
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected $publicOnWeb;

    /**
     * Entity is visible in IS.
     * @var bool
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected $publicInIS;

    /**
     * Entity is visible in portal.
     * @var bool
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected $publicInPortal;

    /**
     * @return bool
     */
    final public function isPublicOnWeb(): bool
    {
        return $this->publicOnWeb ?? false;
    }

    /**
     * @param bool $publicOnWeb
     */
    final public function setPublicOnWeb(bool $publicOnWeb): void
    {
        $this->publicOnWeb = $publicOnWeb;
    }

    /**
     * @return bool
     */
    final public function isPublicInIS(): bool
    {
        return $this->publicInIS ?? false;
    }

    /**
     * @param bool $publicInIS
     */
    final public function setPublicInIS(bool $publicInIS): void
    {
        $this->publicInIS = $publicInIS;
    }

    /**
     * @return bool
     */
    final public function isPublicInPortal(): bool
    {
        return $this->publicInPortal ?? false;
    }

    /**
     * @param bool $publicInPortal
     */
    final public function setPublicInPortal(bool $publicInPortal): void
    {
        $this->publicInPortal = $publicInPortal;
    }
}
