<?php /** @noinspection MethodShouldBeFinalInspection */

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole;
use OswisOrg\OswisCoreBundle\Repository\AppUserRoleRepository;
use Psr\Log\LoggerInterface;

/**
 * AppUserRole service.
 */
class AppUserRoleService
{
    protected EntityManagerInterface $em;

    protected LoggerInterface $logger;

    protected AppUserRoleRepository $appUserRoleRepository;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger, AppUserRoleRepository $appUserRoleRepository)
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->appUserRoleRepository = $appUserRoleRepository;
    }

    public function create(AppUserRole $role): AppUserRole
    {
        $existing = $this->getRepository()->findBySlug($role->getSlug());
        if (null === $existing || !($existing instanceof AppUserRole)) {
            $this->em->persist($role);
            $this->em->flush();
        }

        return $role;
    }

    public function getRepository(): AppUserRoleRepository
    {
        return $this->appUserRoleRepository;
    }
}
