<?php

namespace Zakjakub\OswisCoreBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUserRole;
use Zakjakub\OswisCoreBundle\Entity\AppUserType;
use Zakjakub\OswisCoreBundle\Entity\Nameable;

class AppUserTypeManager
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
        ?AppUserRole $role = null,
        ?bool $adminUser = null
    ): AppUserType {
        $em = $this->em;
        $appUserTypeRepo = $em->getRepository(AppUserType::class);
        $type = $appUserTypeRepo->findOneBy(['name' => $nameable ? $nameable->name : null]);
        if (!$type) {
            $type = new AppUserType($nameable, $role, $adminUser);
            $em->persist($type);
        }
        $em->flush();

        return $type;
    }
}
