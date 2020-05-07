<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Publicity;

/**
 * Trait adds fields that describing visibility of entity.
 */
trait EntityPublicTrait
{
    /**
     * Entity is visible on website.
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     */
    protected ?bool $publicOnWeb = null;

    public function setFieldsFromPublicity(?Publicity $publicity = null): void
    {
        if (null !== $publicity) {
            $this->setPublicOnWeb($publicity->publicOnWeb);
        }
    }

    public function setPublicOnWeb(?bool $publicOnWeb): void
    {
        $this->publicOnWeb = $publicOnWeb;
    }

    public function getPublicity(): Publicity
    {
        return new Publicity($this->isPublicOnWeb());
    }

    public function isPublicOnWeb(): bool
    {
        return $this->publicOnWeb ?? false;
    }
}
