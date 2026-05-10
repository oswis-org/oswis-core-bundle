<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEdit;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEditRequest;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMail;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppUserEditService
{
    public function __construct(
        protected UserPasswordHasherInterface $hasher,
        protected EntityManagerInterface $em,
        protected LoggerInterface $logger,
        protected MailerInterface $mailer,
        protected AppUserService $appUserService,
        protected AppUserMailService $appUserMailService,
    ) {
    }

    /**
     * @param AppUserEdit $userEdit
     *
     * @throws OswisException
     */
    public function assignRequest(AppUserEdit $userEdit): void
    {
        $this->logger->alert('assignRequest()');
        $editRequest = $this->em->getRepository(AppUserEditRequest::class)->findOneBy([
            'token'          => $userEdit->getToken(),
            'userIdentifier' => $userEdit->getUserIdentifier(),
        ]);
        if (!$editRequest instanceof AppUserEditRequest) {
            throw new OswisException('Token není platný.');
        }
        $userEdit->setHasher($this->hasher);
        $userEdit->setUsedEditRequest($editRequest);
    }

    /**
     * @throws OswisException
     * @throws NotImplementedException
     * @throws InvalidTypeException
     * @throws NotFoundException
     */
    public function sendConfirmation(AppUserEdit $userEdit): void
    {
        $this->appUserMailService->sendAppUserEditMail(AppUserMail::TYPE_USER_EDIT, $userEdit->getUsedEditRequest(),
            $userEdit);
    }

}
