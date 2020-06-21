<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Repository;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class AppUserRepository extends EntityRepository implements UserLoaderInterface
{
    public const COLUMN_ACTIVATION_TOKEN = 'activationRequestToken';

    /**
     * @param string|null $username
     *
     * @throws UserNotUniqueException
     * @noinspection MissingParameterTypeDeclarationInspection
     */
    public function loadUserByUsername($username): ?AppUser
    {
        $appUser = $this->findOneByUsernameOrMail($username, true);

        return null !== $appUser && ($appUser instanceof AppUser) && $appUser->isActive() ? $appUser : null;
    }

    /**
     * @param string|null $username
     * @param bool|false  $onlyActive
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
            $builder->andWhere('user.activated <= :now')->andWhere('user.deleted IS NULL OR user.deleted >= :now');
            $builder->setParameter('now', new DateTime());
        }
        try {
            return $builder->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);
        } catch (NonUniqueResultException $e) {
            throw new UserNotUniqueException();
        }
    }

    /**
     * @param int $id
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
            $appUser = $builder->setParameter('id', $id)->getQuery()->getOneOrNullResult(Query::HYDRATE_OBJECT);
        } catch (NonUniqueResultException $e) {
            throw new UserNotUniqueException();
        }

        return ($appUser && ($appUser instanceof AppUser) && $appUser->isActivated()) ? $appUser : null;
    }

    public function findByEmail(string $email): Collection
    {
        $builder = $this->createQueryBuilder('app_user')->where('app_user.email = :email')->setParameter('email', $email);

        return new ArrayCollection(
            $builder->getQuery()->getResult(Query::HYDRATE_OBJECT)
        );
    }

    public function findOneByToken(?string $token): ?AppUser
    {
        return $token ? $this->findOneBy([self::COLUMN_ACTIVATION_TOKEN => $token]) : null;
    }

    final public function findOneBy(array $criteria, array $orderBy = null): ?AppUser
    {
        $result = parent::findOneBy($criteria, $orderBy);

        return $result instanceof AppUser ? $result : null;
    }
}
