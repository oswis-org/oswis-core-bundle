<?php /** @noinspection MethodShouldBeFinalInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait EmailContainerTrait
{
    public function setEmail(?string $email): void
    {
        if ($this->getEmail() !== $email) {
            $newRevision = clone $this->getRevision();
            $newRevision->setEmail($email);
            $this->addRevision($newRevision);
        }
    }

    public function getEmail(?DateTime $dateTime = null): string
    {
        return $this->getRevisionByDate($dateTime)->getEmail();
    }
}
