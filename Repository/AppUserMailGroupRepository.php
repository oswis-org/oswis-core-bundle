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
use OswisOrg\OswisCoreBundle\Interfaces\Mail\MailCategoryInterface;

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

    final public function findByUser(AppUser $appUser, MailCategoryInterface $category): ?AppUserMailGroup
    {
        $queryBuilder = $this->createQueryBuilder('mailGroup');
        $queryBuilder->setParameter("category_id", $category->getId())->setParameter("now", new DateTime());
        $queryBuilder->where("mailGroup.category = :category_id");
        $queryBuilder->andWhere("mailGroup.startDateTime IS NULL OR mailGroup.startDateTime < :now");
        $queryBuilder->andWhere("mailGroup.endDateTime IS NULL OR mailGroup.endDateTime > :now");
        $queryBuilder->orderBy("mailGroup.priority", "DESC");
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
