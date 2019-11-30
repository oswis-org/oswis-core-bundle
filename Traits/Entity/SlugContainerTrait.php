<?php /** @noinspection PhpUnused */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait SlugContainerTrait
{
    final public function setSlug(?string $slug): void
    {
        if ($this->getSlug() !== $slug) {
            $newRevision = clone $this->getRevision();
            $newRevision->setSlug($slug);
            $this->addRevision($newRevision);
        }
    }

    final public function getSlug(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getSlug();
    }
}
