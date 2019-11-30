<?php /** @noinspection PhpUnused */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

/**
 * Trait adds fields that describing visibility of entity.
 */
trait EntityPublicContainerTrait
{
    final public function setPublicOnWeb(bool $publicOnWeb): void
    {
        if ($this->isPublicOnWeb() !== $publicOnWeb) {
            $newRevision = clone $this->getRevision();
            $newRevision->setPublicOnWeb($publicOnWeb);
            $this->addRevision($newRevision);
        }
    }

    final public function isPublicOnWeb(?DateTime $referenceDateTime = null): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->isPublicOnWeb();
    }

    /**
     * @param bool $publicOnWebRoute
     */
    final public function setPublicOnWebRoute(?bool $publicOnWebRoute): void
    {
        if ($this->isPublicOnWebRoute() !== $publicOnWebRoute) {
            $newRevision = clone $this->getRevision();
            $newRevision->setPublicOnWebRoute($publicOnWebRoute);
            $this->addRevision($newRevision);
        }
    }

    final public function isPublicOnWebRoute(?DateTime $referenceDateTime = null): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->isPublicOnWebRoute();
    }

    /**
     * @param bool $publicInIS
     */
    final public function setPublicInIS(?bool $publicInIS): void
    {
        if ($this->isPublicInIS() !== $publicInIS) {
            $newRevision = clone $this->getRevision();
            $newRevision->setPublicInIS($publicInIS);
            $this->addRevision($newRevision);
        }
    }

    final public function isPublicInIS(?DateTime $referenceDateTime = null): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->isPublicInIS;
    }

    /**
     * @param bool $publicInPortal
     */
    final public function setPublicInPortal(?bool $publicInPortal): void
    {
        if ($this->isPublicInPortal() !== $publicInPortal) {
            $newRevision = clone $this->getRevision();
            $newRevision->setPublicInPortal($publicInPortal);
            $this->addRevision($newRevision);
        }
    }

    final public function isPublicInPortal(?DateTime $referenceDateTime = null): bool
    {
        return $this->getRevisionByDate($referenceDateTime)->isPublicInPortal();
    }
}
