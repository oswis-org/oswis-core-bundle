<?php /** @noinspection MethodShouldBeFinalInspection */

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Repository\AppUserRoleRepository;
use Psr\Log\LoggerInterface;

/**
 * AppUserRole service.
 * @noinspection PhpUnused
 */
class AppUserRoleService
{
    protected EntityManagerInterface $em;

    protected LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @param AppUserRole $role
     *
     * @return AppUserRole
     * @throws OswisException
     */
    public function create(AppUserRole $role): AppUserRole
    {
        $existing = $this->getRepository()->findBySlug($role->getSlug());
        if (null === $existing || !($existing instanceof AppUserRole)) {
            $this->em->persist($role);
        }
        $this->em->flush();

        return $role;
    }

    /**
     * @return AppUserRoleRepository
     * @throws OswisException
     */
    public function getRepository(): AppUserRoleRepository
    {
        $repo = $this->em->getRepository(AppUserRole::class);
        if (!($repo instanceof AppUserRoleRepository)) {
            throw new OswisException('Nepodařilo se získat AppUserRoleRepository.');
        }

        return $repo;
    }
}
