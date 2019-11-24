<?php /** @noinspection PhpUnused */

/** @noinspection PhpUndefinedMethodInspection */

namespace Zakjakub\OswisCoreBundle\Traits\Entity;

use DateTime;

trait UrlContainerTrait
{

    /**
     * @param string|null $url
     *
     * @throws RevisionMissingException
     */
    final public function setUrl(?string $url): void
    {
        if ($this->getUrl() !== $url) {
            $newRevision = clone $this->getRevision();
            $newRevision->setUrl($url);
            $this->addRevision($newRevision);
        }
    }

    final public function getUrl(?DateTime $dateTime = null): ?string
    {
        return $this->getRevisionByDate($dateTime)->getUrl();
    }
}
