<?php /** @noinspection MethodShouldBeFinalInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

/**
 * Trait adds fields that describing visibility of entity.
 */
trait EntityPublicContainerTrait
{
    public function setPublicOnWeb(bool $publicOnWeb): void
    {
        if ($this->isPublicOnWeb() !== $publicOnWeb) {
            $newRevision = clone $this->getRevision();
            $newRevision->setPublicOnWeb($publicOnWeb);
            $this->addRevision($newRevision);
        }
    }

    public function isPublicOnWeb(?DateTime $referenceDateTime = null): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->isPublicOnWeb();
    }

    /**
     * @param bool $publicOnWebRoute
     */
    public function setPublicOnWebRoute(?bool $publicOnWebRoute): void
    {
        if ($this->isPublicOnWebRoute() !== $publicOnWebRoute) {
            $newRevision = clone $this->getRevision();
            $newRevision->setPublicOnWebRoute($publicOnWebRoute);
            $this->addRevision($newRevision);
        }
    }

    public function isPublicOnWebRoute(?DateTime $referenceDateTime = null): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->isPublicOnWebRoute();
    }

    /**
     * @param bool $publicInIS
     */
    public function setPublicInIS(?bool $publicInIS): void
    {
        if ($this->isPublicInIS() !== $publicInIS) {
            $newRevision = clone $this->getRevision();
            $newRevision->setPublicInIS($publicInIS);
            $this->addRevision($newRevision);
        }
    }

    public function isPublicInIS(?DateTime $referenceDateTime = null): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->isPublicInIS;
    }

    /**
     * @param bool $publicInPortal
     */
    public function setPublicInPortal(?bool $publicInPortal): void
    {
        if ($this->isPublicInPortal() !== $publicInPortal) {
            $newRevision = clone $this->getRevision();
            $newRevision->setPublicInPortal($publicInPortal);
            $this->addRevision($newRevision);
        }
    }

    public function isPublicInPortal(?DateTime $referenceDateTime = null): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->isPublicInPortal();
    }
}
