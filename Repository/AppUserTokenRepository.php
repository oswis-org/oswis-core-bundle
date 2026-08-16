<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use LogicException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;

class AppUserTokenRepository extends ServiceEntityRepository
{
    /**
     * @param  ManagerRegistry  $registry
     *
     * @throws LogicException
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppUserToken::class);
    }

    final public function findByToken(string $token, int $appUserId): ?AppUserToken
    {
        $queryBuilder = $this->createQueryBuilder('token');
        $queryBuilder->where('token.token = :token')->setParameter('token', $token);
        $queryBuilder->andWhere('token.appUser = :app_user_id')->setParameter('app_user_id', $appUserId);
        // ⚠️ ZÁMĚRNĚ mimo druhoúrovňovou cache: `AppUserToken` ji má zapnutou
        // (`NONSTRICT_READ_WRITE`, životnost 1200 s) a zastaralé čtení by vrátilo token
        // v PŮVODNÍM stavu — tedy jako platný i poté, co byl použit nebo zneplatněn.
        // Jednorázový odkaz (aktivace účtu, obnova hesla) by tak šel použít znovu až 20 minut.
        $query = $queryBuilder->getQuery()->setCacheable(false);
        try {
            return ($result = $query->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT)) instanceof AppUserToken
                ? $result : null;
        } catch (Exception) {
            return null;
        }
    }

    final public function findOneBy(array $criteria, ?array $orderBy = null): ?AppUserToken
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof AppUserToken ? $result : null;
    }
}
