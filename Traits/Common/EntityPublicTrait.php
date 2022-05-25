<?php

/**
 * @noinspection PhpUnused
 * @noinspection PhpFullyQualifiedNameUsageInspection
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Publicity;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

/**
 * Trait adds fields that describing visibility of entity.
 */
trait EntityPublicTrait
{
    /** Indicates whether the item is available on the web. */
    #[Column(type: 'boolean', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    protected ?bool $publicOnWeb = null;

    /**
     * Fill columns related to publicity from Publicity object.
     *
     * @param  Publicity|null  $publicity
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
     * @param  bool|null  $publicOnWeb
     */
    public function setPublicOnWeb(?bool $publicOnWeb): void
    {
        $this->publicOnWeb = $publicOnWeb;
    }

    /**
     * Indicates whether the item is publicly available on the web.
     */
    public function getPublicity(): Publicity
    {
        return new Publicity($this->isPublicOnWeb());
    }

    /**
     * Indicates whether the item is available on the web.
     */
    public function isPublicOnWeb(): bool
    {
        return $this->publicOnWeb ?? false;
    }
}
