<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMail;

class AppUserMailRepository extends ServiceEntityRepository
{
    /**
     * @param  ManagerRegistry  $registry
     *
     * @throws LogicException
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppUserMail::class);
    }

    final public function findByAppUser(AppUser $appUser): Collection
    {
        $queryBuilder = $this->createQueryBuilder('mail');
        $queryBuilder->where("mail.appUser = :app_user_id")->setParameter('app_user_id', $appUser->getId());
        $queryBuilder->addOrderBy('mail.id', 'DESC');
        $result = $queryBuilder->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);

        return new ArrayCollection(is_array($result) ? $result : []);
    }

    final public function findOneBy(array $criteria, array $orderBy = null): ?AppUserMail
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof AppUserMail ? $result : null;
    }
}
