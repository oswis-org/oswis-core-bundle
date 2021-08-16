<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

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
    public function __construct(
        protected EntityManagerInterface $em,
        protected LoggerInterface $logger,
        protected AppUserRoleRepository $appUserRoleRepository
    ) {
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
