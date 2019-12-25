<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 * @noinspection PhpUndefinedMethodInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait ColorContainerTrait
{
    public function setColor(?string $color): void
    {
        if ($this->getColor() !== $color) {
            $newRevision = clone $this->getRevisionByDate();
            $newRevision->setColor($color);
            $this->addRevision($newRevision);
        }
    }

    public function getColor(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getColor();
    }

    public function isForegroundWhite(?DateTime $dateTime = null): string
    {
        return $this->getRevisionByDate($dateTime)->isForegroundWhite();
    }
}
