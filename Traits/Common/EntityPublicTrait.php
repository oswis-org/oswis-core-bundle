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
     * @param Publicity|null $publicity
     */
    public function setFieldsFromPublicity(?Publicity $publicity = null): void
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
     * @return Publicity
     */
    public function getPublicity(): Publicity
    {
        return new Publicity($this->isPublicOnWeb());
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
