<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Gedmo\Mapping\Annotation\Blameable;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\MaxDepth;

trait BlameableTrait
{
    #[ManyToOne(targetEntity: AppUser::class)]
    #[JoinColumn(name: 'created_author_id', referencedColumnName: 'id')]
    #[MaxDepth(1)]
    #[Blameable(on: 'create')]
    #[ApiFilter(SearchFilter::class, properties: [
        "createdBy.id"       => "exact",
        "createdBy.username" => "ipartial",
        "createdBy.name"     => "ipartial",
    ])]
    #[ApiFilter(OrderFilter::class, properties: ["createdBy.id", "createdBy.username", "createdBy.name"])]
    #[ApiFilter(ExistsFilter::class)]
    protected ?AppUser $createdBy = null;

    #[ManyToOne(targetEntity: AppUser::class)]
    #[JoinColumn(name: 'updated_author_id', referencedColumnName: 'id')]
    #[MaxDepth(1)]
    #[Blameable(on: 'update')]
    #[ApiFilter(SearchFilter::class, properties: [
        "updatedBy.id"       => "exact",
        "updatedBy.username" => "ipartial",
        "updatedBy.name"     => "ipartial",
    ])]
    #[ApiFilter(OrderFilter::class, properties: ["updatedBy.id", "updatedBy.username", "updatedBy.name"])]
    #[ApiFilter(ExistsFilter::class)]
    protected ?AppUser $updatedBy = null;

    public function getUpdatedBy(): ?AppUser
    {
        return $this->updatedBy;
    }

    public function getCreatedBy(): ?AppUser
    {
        return $this->createdBy;
    }
}
