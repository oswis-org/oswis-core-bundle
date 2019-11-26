<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Entity\AbstractClass;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use Zakjakub\OswisCoreBundle\Exceptions\RevisionMissingException;
use Zakjakub\OswisCoreBundle\Interfaces\RevisionContainerInterface;

/**
 * Abstract class representing container of revisions/versions of some entity (of some entity which extends AbstractRevision).
 *
 * @author Jakub Zak <mail@jakubzak.eu>
 */
abstract class AbstractRevisionContainer implements RevisionContainerInterface
{
    /**
     * Revisions/versions of this container.
     * @var Collection
     */
    protected Collection $revisions;

    /**
     * Revision/version which is actual/active now.
     * @var AbstractRevision|null
     */
    protected ?AbstractRevision $activeRevision;

    /**
     * Check validity of some revision/version (ie. for use before adding revision).
     *
     * @param AbstractRevision|null $revision
     */
    abstract public static function checkRevision(?AbstractRevision $revision): void;

    /**
     * Class name of revisions/versions stored in this container.
     * @return string
     */
    abstract public static function getRevisionClassName(): string;

    /**
     * Add some revision/version to this container.
     *
     * @param AbstractRevision|null $revision
     */
    final public function addRevision(?AbstractRevision $revision): void
    {
        if (!$revision) {
            return;
        }
        static::checkRevision($revision);
        if (!$this->revisions->contains($revision)) {
            $this->revisions->add($revision);
            $revision->setContainer($this);
        }
        $this->setActiveRevision($revision);
    }

    /**
     * Remove some revision/version from this container.
     *
     * @param AbstractRevision|null $revision
     */
    final public function removeRevision(?AbstractRevision $revision): void
    {
        if (!$revision) {
            return;
        }
        static::checkRevision($revision);
        if ($this->revisions->removeElement($revision)) {
            $revision->setContainer(null);
        }
        if ($revision === $this->getActiveRevision()) {
            $this->updateActiveRevision();
        }
    }

    /**
     * Revision/version which is actual/active now.
     * @return AbstractRevision|null
     */
    final public function getActiveRevision(): ?AbstractRevision
    {
        if (!$this->activeRevision) {
            $this->updateActiveRevision();
        }

        return $this->activeRevision;
    }

    /** @noinspection MethodShouldBeFinalInspection */

    /**
     * Set revision/version which is actual/active now.
     *
     * @param AbstractRevision $activeRevision
     */
    final public function setActiveRevision(?AbstractRevision $activeRevision): void
    {
        $this->activeRevision = $activeRevision;
    }

    /**
     * Automatically set revision/version which is actual/active now.
     */
    public function updateActiveRevision(): void
    {
        try {
            $lastRevision = $this->getRevision(null, true);
            if ($lastRevision !== $this->activeRevision) {
                $this->activeRevision = $lastRevision;
            }
        } catch (RevisionMissingException $e) {
            return;
        }
    }

    /**
     * Get revision/version which is (was) active in specified date and time (or now if dateTime is not specified).
     *
     * @param DateTime|null $dateTime
     *
     * @param bool|null     $force
     *
     * @return AbstractRevision
     * @throws RevisionMissingException
     */
    final public function getRevision(DateTime $dateTime = null, ?bool $force = false): AbstractRevision
    {
        if (!$force && !$dateTime && $this->activeRevision) {
            return $this->activeRevision;
        }
        $revisions = $this->getRevisionsOlderThanDateTime($dateTime);
        if (!$revisions || !$revisions[0]) {
            throw new RevisionMissingException((static::getRevisionClassName() ?? 'Revision class').' not found.');
        }
        static::checkRevision($revisions[0]);
        if (!$dateTime && (!$this->activeRevision || $this->activeRevision !== $revisions[0])) {
            $this->activeRevision = $revisions[0];
        }

        return $revisions[0];
    }

    /**
     * @param DateTime|null $dateTime
     *
     * @return array
     */
    final public function getRevisionsOlderThanDateTime(DateTime $dateTime = null): array
    {
        try {
            $dateTime = $dateTime ?? new DateTime() ?? null;
        } catch (Exception $e) {
            $dateTime = null;
        }
        $revisions = $this->getRevisions()->filter(fn(AbstractRevision $revision) => $dateTime >= $revision->getCreatedDateTime())->toArray();
        AbstractRevision::sortByCreatedDateTime($revisions);

        return $revisions;
    }

    /**
     * All revisions/versions of this container.
     * @return Collection
     */
    final public function getRevisions(): Collection
    {
        return $this->revisions ?? new ArrayCollection();
    }

    /**
     * Get date and time of active/actual revision/version in some date and time (or now if referenceDateTime is not specified).
     *
     * @param DateTime|null $referenceDateTime
     *
     * @return DateTime|null
     * @throws RevisionMissingException
     */
    final public function getLastRevisionDateTime(?DateTime $referenceDateTime = null): ?DateTime
    {
        return $this->getRevision($referenceDateTime)->getCreatedDateTime();
    }
}
