<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserType;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Repository\AppUserTypeRepository;
use Psr\Log\LoggerInterface;

class AppUserTypeService
{
    protected EntityManagerInterface $em;

    protected LoggerInterface $logger;

    public function __construct(EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    /**
     * @param AppUserType $type
     *
     * @return AppUserType
     * @throws OswisException
     */
    public function create(AppUserType $type): AppUserType
    {
        $existing = $this->getRepository()->findBySlug($type->getSlug());
        if (null === $existing || !($existing instanceof AppUserType)) {
            $this->em->persist($type);
            $this->em->flush();
        }

        return $type;
    }

    /**
     * @return AppUserTypeRepository
     * @throws OswisException
     */
    public function getRepository(): AppUserTypeRepository
    {
        $repo = $this->em->getRepository(AppUserType::class);
        if (!($repo instanceof AppUserTypeRepository)) {
            throw new OswisException('Nepodařilo se získat AppUserTypeRepository.');
        }

        return $repo;
    }
}
