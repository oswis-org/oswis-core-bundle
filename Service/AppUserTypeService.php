<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace Zakjakub\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUserRole;
use Zakjakub\OswisCoreBundle\Entity\AppUserType;
use Zakjakub\OswisCoreBundle\Entity\Nameable;
use Zakjakub\OswisCoreBundle\Repository\AppUserTypeRepository;

class AppUserTypeService
{
    protected EntityManagerInterface $em;

    protected ?LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, ?LoggerInterface $logger = null)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function create(?Nameable $nameable = null, ?AppUserRole $role = null, ?bool $adminUser = null): AppUserType
    {
        $type = $nameable ? $this->getRepository()->findBySlug($nameable->slug) : null;
        if (null === $type || !($type instanceof AppUserType)) {
            $this->em->persist(new AppUserType($nameable, $role, $adminUser));
        }
        $this->em->flush();

        return $type;
    }

    public function getRepository(): AppUserTypeRepository
    {
        $repo = $this->em->getRepository(AppUserType::class);
        assert($repo instanceof AppUserTypeRepository);

        return $repo;
    }
}
