<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Entity\AppUserMail;

use OswisOrg\OswisCoreBundle\Entity\AbstractClass\AbstractMailGroup;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Interfaces\EMail\EMailCategoryInterface;

/**
 * @Doctrine\ORM\Mapping\Entity(repositoryClass="OswisOrg\OswisCoreBundle\Repository\AppUserMailGroupRepository")
 * @Doctrine\ORM\Mapping\Table(name="core_app_user_e_mail_group")
 * @ApiPlatform\Core\Annotation\ApiResource(
 *   attributes={
 *     "filters"={"search"},
 *     "access_control"="is_granted('ROLE_ADMIN')",
 *     "normalization_context"={"groups"={"app_user_e_mail_groups_get"}, "enable_max_depth"=true},
 *     "denormalization_context"={"groups"={"app_user_e_mail_groups_post"}, "enable_max_depth"=true}
 *   },
 *   collectionOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_e_mail_groups_get"}, "enable_max_depth"=true},
 *     },
 *     "post"={
 *       "access_control"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"app_user_e_mail_groups_post"}, "enable_max_depth"=true}
 *     }
 *   },
 *   itemOperations={
 *     "get"={
 *       "access_control"="is_granted('ROLE_ADMIN')",
 *       "normalization_context"={"groups"={"app_user_e_mail_group_get"}, "enable_max_depth"=true},
 *     },
 *     "put"={
 *       "access_control"="is_granted('ROLE_ADMIN')",
 *       "denormalization_context"={"groups"={"app_user_e_mail_group_put"}, "enable_max_depth"=true}
 *     }
 *   }
 * )
 * @OswisOrg\OswisCoreBundle\Filter\SearchAnnotation({"id"})
 * @author Jakub Zak <mail@jakubzak.eu>
 * @Doctrine\ORM\Mapping\Cache(usage="NONSTRICT_READ_WRITE", region="core_app_user")
 */
class AppUserMailGroup extends AbstractMailGroup
{
    /**
     * @Doctrine\ORM\Mapping\ManyToOne(targetEntity="AppUserMailCategory", fetch="EAGER")
     * @Doctrine\ORM\Mapping\JoinColumn(nullable=true)
     */
    protected ?EMailCategoryInterface $category = null;

    public function isApplicableByRestrictions(?object $entity): bool
    {
        return $entity instanceof AppUser;
    }
}
