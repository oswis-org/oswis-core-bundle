<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Repository;

use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use LogicException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMailGroup;
use OswisOrg\OswisCoreBundle\Interfaces\EMail\EMailCategoryInterface;

class AppUserMailGroupRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     *
     * @throws LogicException
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppUserMailGroup::class);
    }

    final public function findByUser(AppUser $appUser, EMailCategoryInterface $category): ?AppUserMailGroup
    {
        $queryBuilder = $this->createQueryBuilder('group');
        $queryBuilder->setParameter("category_id", $category->getId())->setParameter("now", new DateTime());
        $queryBuilder->where("group.category = :category_id");
        $queryBuilder->andWhere("group.startDateTime IS NULL OR group.startDateTime < :now");
        $queryBuilder->andWhere("group.endDateTime IS NULL OR group.endDateTime > :now");
        $queryBuilder->orderBy("group.priority", "DESC");
        try {
            $appUserEMailGroups = $queryBuilder->getQuery()->getResult();
            foreach ($appUserEMailGroups as $appUserEMailGroup) {
                if ($appUserEMailGroup instanceof AppUserMailGroup && $appUserEMailGroup->isApplicable($appUser)) {
                    return $appUserEMailGroup;
                }
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    final public function findOneBy(array $criteria, array $orderBy = null): ?AppUserMailGroup
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof AppUserMailGroup ? $result : null;
    }
}
