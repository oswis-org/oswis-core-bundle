<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUndefinedMethodInspection
 */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait TypeContainerTrait
{
    public function setType(?string $type): void
    {
        if ($this->getType() !== $type) {
            $newRevision = clone $this->getRevision();
            $newRevision->setType($type);
            $this->addRevision($newRevision);
        }
    }

    public function getType(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getType();
    }
}
