<?php /** @noinspection MethodShouldBeFinalInspection */
/** @noinspection PhpUnused */
/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait PhoneContainerTrait
{
    public function setPhone(?string $phone): void
    {
        if ($this->getPhone() !== $phone) {
            $newRevision = clone $this->getRevision();
            $newRevision->setPhone($phone);
            $this->addRevision($newRevision);
        }
    }

    public function getPhone(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getPhone();
    }
}
