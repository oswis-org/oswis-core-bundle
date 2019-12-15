<?php

namespace Zakjakub\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUserRole;
use Zakjakub\OswisCoreBundle\Entity\AppUserType;
use Zakjakub\OswisCoreBundle\Entity\Nameable;

/**
 * AppUserType service.
 * @noinspection PhpUnused
 */
class AppUserTypeService
{
    protected EntityManagerInterface $em;

    protected ?LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, ?LoggerInterface $logger = null)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    final public function create(?Nameable $nameable = null, ?AppUserRole $role = null, ?bool $adminUser = null): AppUserType
    {
        $type = $this->em->getRepository(AppUserType::class)->findOneBy(['slug' => $nameable ? $nameable->slug : null]);
        if (!$type) {
            $type = new AppUserType($nameable, $role, $adminUser);
            $this->em->persist($type);
        }
        $this->em->flush();

        // TODO: Log it.
        return $type;
    }
}
