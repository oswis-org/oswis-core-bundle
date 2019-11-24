<?php /** @noinspection PhpUnused */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait PhoneContainerTrait
{

    /**
     * @param string|null $phone
     *
     * @throws RevisionMissingException
     */
    final public function setPhone(?string $phone): void
    {
        if ($this->getPhone() !== $phone) {
            $newRevision = clone $this->getRevision();
            $newRevision->setPhone($phone);
            $this->addRevision($newRevision);
        }
    }

    final public function getPhone(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getPhone();
    }
}
