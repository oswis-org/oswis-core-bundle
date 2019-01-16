<?php

namespace Zakjakub\OswisResourcesBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Zakjakub\OswisResourcesBundle\Entity\AppUserRole;
use Zakjakub\OswisResourcesBundle\Entity\Nameable;

class AppUserRoleManager
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(
        EntityManagerInterface $em,
        ?LoggerInterface $logger = null
    ) {
        $this->em = $em;
        $this->logger = $logger;
    }

    final public function create(
        ?Nameable $nameable = null,
        ?string $roleString = null,
        ?AppUserRole $parent = null
    ): AppUserRole {
        $em = $this->em;
        $appUserRoleRepo = $em->getRepository(AppUserRole::class);
        $role = $appUserRoleRepo->findOneBy(['name' => $nameable ? $nameable->name : null]);
        if (!$role) {
            $role = new AppUserRole($nameable, $roleString, $parent);
            $em->persist($role);
        }
        $em->flush();

        return $role;
    }
}
