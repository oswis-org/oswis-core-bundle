<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait DescriptionContainerTrait
{
    final public function setDescription(?string $description): void
    {
        if ($this->getDescription() !== $description) {
            $newRevision = clone $this->getRevision();
            $newRevision->setDescription($description);
            $this->addRevision($newRevision);
        }
    }

    final public function getDescription(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getDescription();
    }
}
