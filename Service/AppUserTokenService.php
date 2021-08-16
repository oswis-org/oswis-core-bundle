<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Repository\AppUserTokenRepository;
use Psr\Log\LoggerInterface;

/**
 * AppUserRole service.
 */
class AppUserTokenService
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected LoggerInterface $logger,
        protected AppUserTokenRepository $appUserTokenRepository,
    ) {
    }

    /**
     * @param  AppUser  $appUser
     * @param  string|null  $type
     * @param  bool|null  $multipleUseAllowed
     * @param  int|null  $validHours
     *
     * @return AppUserToken
     * @throws InvalidTypeException
     */
    public function create(AppUser $appUser, ?string $type = null, ?bool $multipleUseAllowed = null, ?int $validHours = null): AppUserToken
    {
        try {
            $appUserToken = new AppUserToken(
                $appUser, $appUser->getEmail(), $type, $multipleUseAllowed ?? false, $validHours,
            );
            $this->em->persist($appUserToken);
            $this->em->flush();
            $tokenId = $appUserToken->getId();
            $appUserId = $appUser->getId();
            $this->logger->info("Created new token ($tokenId) of type '$type' for user '$appUserId'.");

            return $appUserToken;
        } catch (InvalidTypeException $exception) {
            $this->logger->error($exception->getMessage());
            throw $exception;
        }
    }

    public function getRepository(): AppUserTokenRepository
    {
        return $this->appUserTokenRepository;
    }
}
