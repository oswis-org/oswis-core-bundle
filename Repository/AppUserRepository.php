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
            $builder = $this->createQueryBuilder('u')->where('(u.id = :id) AND'); // TODO: Is in range??????!!!!!!
            $appUser = $builder->setParameter('id', $id)->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
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
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException
     */
    public function loadUserByIdentifier(string $identifier): null|UserInterface
    {
        return $this->loadUserByUsername($identifier);
    }

    /**
     * @param  string|null  $username
     *
     * @return AppUser|null
     * @throws UserNotUniqueException
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    public function loadUserByUsername(?string $username): ?AppUser
    {
        $appUser = $this->findOneByUsernameOrMail($username, true);

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
