<?php

/**
 * @noinspection PhpUnused
 * @noinspection PhpFullyQualifiedNameUsageInspection
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Publicity;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

/** Trait adds fields that describe visibility of entity. */
trait EntityPublicTrait
{
    /** Indicates whether the item is available on the web. */
    #[Column(type: 'boolean', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(BooleanFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    protected ?bool $publicOnWeb = null;

    /** Indicates whether the item is available in the app. */
    #[Column(type: 'boolean', nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(BooleanFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    protected ?bool $publicInApp = null;

    /** Fill columns related to publicity from Publicity object. */
    public function setFieldsFromPublicity(?Publicity $publicity = null): void
    {
        if (null !== $publicity) {
            $this->setPublicOnWeb($publicity->publicOnWeb);
            $this->setPublicInApp($publicity->publicInApp);
        }
    }

    /** Sets whether the item is publicly available on the web. */
    public function setPublicOnWeb(?bool $publicOnWeb): void
    {
        $this->publicOnWeb = $publicOnWeb;
    }

    public function setPublicInApp(?bool $publicInApp): void
    {
        $this->publicInApp = $publicInApp;
    }

    public function getPublicity(): Publicity
    {
        return new Publicity($this->isPublicOnWeb(), $this->isPublicInApp());
    }

    /** Indicates whether the item is available on the web. */
    public function isPublicOnWeb(): bool
    {
        return $this->publicOnWeb ?? false;
    }

    public function isPublicInApp(): bool
    {
        return $this->publicInApp ?? false;
    }
}
