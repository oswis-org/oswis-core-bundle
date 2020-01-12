<?php /** @noinspection MethodShouldBeFinalInspection */

namespace Zakjakub\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUserRole;
use Zakjakub\OswisCoreBundle\Entity\Nameable;
use Zakjakub\OswisCoreBundle\Repository\AppUserRoleRepository;

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

    public function create(?Nameable $nameable = null, ?string $roleString = null, ?AppUserRole $parent = null): AppUserRole
    {
        $role = $nameable ? $this->getRepository()->findBySlug($nameable->slug) : null;
        if (null === $role || !($role instanceof AppUserRole)) {
            $role = new AppUserRole($nameable, $roleString, $parent);
            $this->em->persist($role);
        }
        $this->em->flush();

        return $role;
    }

    public function getRepository(): AppUserRoleRepository
    {
        $repo = $this->em->getRepository(AppUserRole::class);
        assert($repo instanceof AppUserRoleRepository);

        return $repo;
    }
}
