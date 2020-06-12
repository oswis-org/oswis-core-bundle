<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Exception;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;

class AppUserTokenRepository extends EntityRepository
{
    final public function findByToken(string $token, int $appUserId): ?AppUserToken
    {
        $queryBuilder = $this->createQueryBuilder('token');
        $queryBuilder->where('token.token = :token')->setParameter('token', $token);
        $queryBuilder->andWhere('token.appUser = :app_user_id')->setParameter('app_user_id', $appUserId);
        $query = $queryBuilder->getQuery();
        try {
            return $query->getOneOrNullResult(Query::HYDRATE_OBJECT);
        } catch (Exception $e) {
            return null;
        }
    }

    final public function findOneBy(array $criteria, array $orderBy = null): ?AppUserToken
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof AppUserToken ? $result : null;
    }
}
