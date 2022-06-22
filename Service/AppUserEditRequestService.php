<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEditRequest;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMail;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;

class AppUserEditRequestService
{
    public function __construct(
        protected EntityManagerInterface $em,
        protected LoggerInterface $logger,
        protected MailerInterface $mailer,
        protected AppUserService $appUserService,
        protected AppUserMailService $appUserMailService,
    ) {
    }

    /**
     * @param  \OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEditRequest  $userEditRequest
     *
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException
     */
    public function assignAppUser(AppUserEditRequest $userEditRequest): void
    {
        $appUser = $this->appUserService->getRepository()->findOneByUsernameOrMail(''
                                                                                   .$userEditRequest->getUserIdentifier(),
            true);
        if ($appUser === null) {
            throw new UserNotFoundException();
        }
        $userEditRequest->setAppUser($appUser);
    }

    /**
     * @param  \OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEditRequest  $userEditRequest
     *
     * @return void
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotFoundException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\OswisException
     */
    public function sendMail(AppUserEditRequest $userEditRequest): void
    {
        $this->appUserMailService->sendAppUserEditMail(AppUserMail::TYPE_USER_EDIT_REQUEST, $userEditRequest);
    }

}
