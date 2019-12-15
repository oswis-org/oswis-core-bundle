<?php

namespace Zakjakub\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUserRole;
use Zakjakub\OswisCoreBundle\Entity\Nameable;

/**
 * AppUserRole service.
 * @noinspection PhpUnused
 */
class AppUserRoleService
{
    protected EntityManagerInterface $em;

    protected ?LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, ?LoggerInterface $logger = null)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    final public function create(?Nameable $nameable = null, ?string $roleString = null, ?AppUserRole $parent = null): AppUserRole
    {
        $role = $this->em->getRepository(AppUserRole::class)->findOneBy(['name' => $nameable ? $nameable->name : null]);
        if (!$role) {
            $role = new AppUserRole($nameable, $roleString, $parent);
            $this->em->persist($role);
        }
        $this->em->flush();

        // TODO: Log it.
        return $role;
    }
}
