<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace Zakjakub\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Zakjakub\OswisCoreBundle\Entity\AppUserType;
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

    public function create(AppUserType $type): AppUserType
    {
        $existing = $this->getRepository()->findBySlug($type->getSlug());
        if (null === $existing || !($existing instanceof AppUserType)) {
            $this->em->persist($type);
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
