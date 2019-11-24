<?php /** @noinspection PhpUnused */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait EmailContainerTrait
{

    /**
     * @param string|null $email
     *
     * @throws RevisionMissingException
     */
    final public function setEmail(?string $email): void
    {
        if ($this->getEmail() !== $email) {
            $newRevision = clone $this->getRevision();
            $newRevision->setEmail($email);
            $this->addRevision($newRevision);
        }
    }

    final public function getEmail(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getEmail();
    }
}
