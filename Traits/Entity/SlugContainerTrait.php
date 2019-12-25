<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 * @noinspection PhpUndefinedMethodInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait SlugContainerTrait
{
    public function setSlug(?string $slug): void
    {
        if ($this->getSlug() !== $slug) {
            $newRevision = clone $this->getRevision();
            $newRevision->setSlug($slug);
            $this->addRevision($newRevision);
        }
    }

    public function getSlug(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getSlug();
    }
}
