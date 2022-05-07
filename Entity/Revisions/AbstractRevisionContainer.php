<?php
/** @noinspection PhpUnused */

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\Revisions;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Interfaces\Revisions\RevisionContainerInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Revisions\RevisionInterface;

/**
 * Abstract class representing container of revisions/versions of some entity (of some entity which extends AbstractRevision).
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractRevisionContainer implements RevisionContainerInterface
{
    /**
     * Revisions/versions of this container.
     *
     * @var Collection<AbstractRevision>|null
     */
    protected ?Collection $revisions = null;

    /**
     * Revision/version which is actual/active now.
     *
     * @var AbstractRevision|null
     */
    protected ?AbstractRevision $activeRevision = null;

    /**
     * Class name of revisions/versions stored in this container.
     */
    abstract public static function getRevisionClassName(): string;

    /**
     * Set revision/version which is actual/active now.
     *
     * @param  AbstractRevision  $activeRevision
     */
    final public function setActiveRevision(?AbstractRevision $activeRevision): void
    {
        $this->activeRevision = $activeRevision;
    }

    final public function getRevisionsOlderThanDateTime(DateTime $dateTime = null): array
    {
        $dateTime ??= new DateTime();
        $revisions = $this->getRevisions()->filter(function (mixed $revision) use ($dateTime) {
            return $revision instanceof AbstractRevision && $dateTime >= $revision->getCreatedAt();
        })->toArray();
        AbstractRevision::sortByCreatedAt($revisions);

        return $revisions;
    }

    /**
     * All revisions/versions of this container.
     * @return Collection<RevisionInterface>
     */
    final public function getRevisions(): Collection
    {
        return $this->revisions ?? new ArrayCollection();
    }

    /**
     * Get date and time of active/actual revision/version in some date and time (or now if referenceDateTime is not specified).
     */
    final public function getLastRevisionDateTime(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getRevision($referenceDateTime)->getCreatedAt();
    }
}
