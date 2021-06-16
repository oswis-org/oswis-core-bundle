<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserType;
use OswisOrg\OswisCoreBundle\Repository\AppUserTypeRepository;
use Psr\Log\LoggerInterface;

class AppUserTypeService
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected LoggerInterface $logger,
        protected AppUserTypeRepository $appUserTypeRepository,
    ) {
    }

    public function create(AppUserType $type): AppUserType
    {
        $existing = $this->getRepository()->findBySlug($type->getSlug());
        if (null === $existing || !($existing instanceof AppUserType)) {
            $this->em->persist($type);
            $this->em->flush();
        }

        return $type;
    }

    public function getRepository(): AppUserTypeRepository
    {
        return $this->appUserTypeRepository;
    }
}
