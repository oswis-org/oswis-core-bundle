<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\AppUserMail;

use Doctrine\ORM\Mapping\Cache;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractMailGroup;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\DateTimeRange;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Entity\TwigTemplate\TwigTemplate;
use OswisOrg\OswisCoreBundle\Repository\AppUserMailGroupRepository;

/**
 * @author Jakub Zak <mail@jakubzak.eu>
 * @OswisOrg\OswisCoreBundle\Filter\SearchAnnotation({"id"})
 * @ApiPlatform\Core\Annotation\ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "security"="is_granted('ROLE_ADMIN')",
 *     "normalization_context"={"groups"={"app_user_mail_groups_get"}, "enable_max_depth"=true},
 *     "denormalization_context"={"groups"={"app_user_mail_groups_post"}, "enable_max_depth"=true}
 *   },
 *   collectionOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_mail_groups_get"}, "enable_max_depth"=true},
 *     },
 *     "post"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"app_user_mail_groups_post"}, "enable_max_depth"=true}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_mail_group_get"}, "enable_max_depth"=true},
 *     },
 *     "put"={
 *       "security"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"app_user_mail_group_put"}, "enable_max_depth"=true}
 *     }
 *   }
 * )
 */
#[Entity(repositoryClass: AppUserMailGroupRepository::class)]
#[Table(name: 'core_app_user_mail_group')]
#[Cache(usage: 'NONSTRICT_READ_WRITE', region: 'core_app_user_mail')]
class AppUserMailGroup extends AbstractMailGroup
{
    #[ManyToOne(targetEntity: AppUserMailCategory::class, fetch: 'EAGER')]
    #[JoinColumn(nullable: true)]
    protected ?AppUserMailCategory $category = null;

    public function __construct(
        ?Nameable $nameable = null,
        ?int $priority = null,
        ?DateTimeRange $range = null,
        ?TwigTemplate $twigTemplate = null,
        bool $automaticMailing = false,
        ?AppUserMailCategory $appUserMailCategory = null
    ) {
        parent::__construct($nameable, $priority, $range, $twigTemplate, $automaticMailing);
        $this->setCategory($appUserMailCategory);
    }

    public function isApplicableByRestrictions(?object $entity): bool
    {
        return $entity instanceof AppUser;
    }

    public function isCategory(?AppUserMailCategory $category): bool
    {
        return $this->getCategory() === $category;
    }

    public function getCategory(): ?AppUserMailCategory
    {
        return $this->category;
    }

    public function setCategory(?AppUserMailCategory $category): void
    {
        $this->category = $category;
    }

    public function isType(?string $type): bool
    {
        return $this->getType() === $type;
    }

    public function getType(): ?string
    {
        return $this->getCategory()?->getType();
    }
}
