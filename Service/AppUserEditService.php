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
     * @param  \OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEdit  $userEdit
     *
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\OswisException
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
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\OswisException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotFoundException
     */
    public function sendConfirmation(AppUserEdit $userEdit): void
    {
        $this->appUserMailService->sendAppUserEditMail(AppUserMail::TYPE_USER_EDIT, $userEdit->getUsedEditRequest(),
            $userEdit);
    }

}
