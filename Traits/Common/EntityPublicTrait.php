<?php
/**
 * @noinspection PhpFullyQualifiedNameUsageInspection
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Traits\Common;

/**
 * Trait adds fields that describing visibility of entity.
 */
trait EntityPublicTrait
{
    /**
     * Indicates whether the item is available on the web.
     * @Doctrine\ORM\Mapping\Column(type="boolean", nullable=true)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter::class, strategy="exact")
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter::class)
     * @ApiPlatform\Core\Annotation\ApiFilter(ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter::class)
     */
    protected ?bool $publicOnWeb = null;

    /**
     * Fill columns related to publicity from Publicity object.
     *
     * @param \OswisOrg\OswisCoreBundle\Entity\NonPersistent\Publicity|null $publicity
     */
    public function setFieldsFromPublicity(?\OswisOrg\OswisCoreBundle\Entity\NonPersistent\Publicity $publicity = null): void
    {
        if (null !== $publicity) {
            $this->setPublicOnWeb($publicity->publicOnWeb);
        }
    }

    /**
     * Sets whether the item is publicly available on the web.
     *
     * @param bool|null $publicOnWeb
     */
    public function setPublicOnWeb(?bool $publicOnWeb): void
    {
        $this->publicOnWeb = $publicOnWeb;
    }

    /**
     * Indicates whether the item is publicly available on the web.
     * @return \OswisOrg\OswisCoreBundle\Entity\NonPersistent\Publicity
     */
    public function getPublicity(): \OswisOrg\OswisCoreBundle\Entity\NonPersistent\Publicity
    {
        return new \OswisOrg\OswisCoreBundle\Entity\NonPersistent\Publicity($this->isPublicOnWeb());
    }

    /**
     * Indicates whether the item is available on the web.
     * @return bool
     */
    public function isPublicOnWeb(): bool
    {
        return $this->publicOnWeb ?? false;
    }
}
