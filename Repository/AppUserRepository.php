<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Repository;

use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AppUserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    /**
     * @param  ManagerRegistry  $registry
     *
     * @throws LogicException
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AppUser::class);
    }

    /**
     * Admin user list: newest first, optionally narrowed by a free-text query over
     * name / username / e-mail (substring). Capped at $limit so the page stays fast;
     * WITH a query the admin can reach any user beyond the default window (fixes the
     * old hard 500-newest cap that hid the bulk of the user base).
     *
     * @return list<AppUser>
     */
    public function searchForAdmin(?string $q, int $limit = 500): array
    {
        $builder = $this->createQueryBuilder('u')->orderBy('u.id', 'DESC')->setMaxResults(max(1, $limit));
        if (null !== $q && '' !== $q) {
            $builder->andWhere('u.name LIKE :q OR u.username LIKE :q OR u.email LIKE :q')
                ->setParameter('q', '%'.$q.'%');
        }
        /** @var list<AppUser> $result */
        $result = $builder->getQuery()->getResult();

        return $result;
    }

    /**
     * Real counts over the WHOLE user base (not just the loaded window) for the admin
     * filter tabs. Mirrors AppUser::isActivated()/isDeleted(): active = activated in the
     * past and not (yet) deleted; deleted = deletedAt in the past; inactive = the rest.
     *
     * @return array{all: int, active: int, inactive: int, deleted: int}
     */
    public function adminStatusCounts(?DateTime $now = null): array
    {
        $now ??= new DateTime();
        $all = (int) $this->createQueryBuilder('a')->select('COUNT(a.id)')
            ->getQuery()->getSingleScalarResult();
        $deleted = (int) $this->createQueryBuilder('a')->select('COUNT(a.id)')
            ->andWhere('a.deletedAt IS NOT NULL AND a.deletedAt < :now')->setParameter('now', $now)
            ->getQuery()->getSingleScalarResult();
        $active = (int) $this->createQueryBuilder('a')->select('COUNT(a.id)')
            ->andWhere('a.activated IS NOT NULL AND a.activated <= :now')
            ->andWhere('(a.deletedAt IS NULL OR a.deletedAt >= :now)')->setParameter('now', $now)
            ->getQuery()->getSingleScalarResult();

        return ['all' => $all, 'active' => $active, 'inactive' => $all - $active, 'deleted' => $deleted];
    }

    /**
     * @param  int  $id
     *
     * @throws UserNotUniqueException
     */
    public function loadUserById(?int $id): ?AppUser
    {
        if (!$id) {
            return null;
        }
        try {
            $builder = $this->createQueryBuilder('u')
                ->where('u.id = :id')
                ->andWhere('u.activated <= :now')
                ->andWhere('u.deletedAt IS NULL OR u.deletedAt >= :now')
                ->setParameter('id', $id)
                ->setParameter('now', new DateTime());
            $appUser = $builder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
        } catch (NonUniqueResultException) {
            throw new UserNotUniqueException();
        }

        return (($appUser instanceof AppUser) && $appUser->isActivated()) ? $appUser : null;
    }

    public function findByEmail(string $email): Collection
    {
        $builder = $this->createQueryBuilder('app_user')
                        ->where('app_user.email = :email')
                        ->setParameter('email', $email);

        return new ArrayCollection(is_array($result = $builder->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT))
            ? $result : [],);
    }

    final public function findOneBy(array $criteria, ?array $orderBy = null): ?AppUser
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof AppUser ? $result : null;
    }

    /**
     * @throws UserNotUniqueException
     */
    public function loadUserByIdentifier(string $identifier): null|UserInterface
    {
        $appUser = $this->findOneByUsernameOrMail($identifier, true);

        return ($appUser instanceof AppUser) && $appUser->isActive() ? $appUser : null;
    }

    /**
     * @param  string|null  $username
     * @param  bool|false  $onlyActive
     *
     * @return AppUser|null
     * @throws UserNotUniqueException
     */
    public function findOneByUsernameOrMail(?string $username, bool $onlyActive = false): ?AppUser
    {
        if (empty($username)) {
            return null;
        }
        $builder = $this->createQueryBuilder('user')->where('(user.username = :username OR user.email = :username)');
        $builder->setParameter('username', $username);
        if (true === $onlyActive) {
            $builder->andWhere('user.activated <= :now')->andWhere('user.deletedAt IS NULL OR user.deletedAt >= :now');
            $builder->setParameter('now', new DateTime());
        }
        try {
            return ($result = $builder->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT))
                   instanceof
                   AppUser ? $result : null;
        } catch (NonUniqueResultException) {
            throw new UserNotUniqueException();
        }
    }
}
