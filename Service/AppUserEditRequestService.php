<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEditRequest;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMail;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\UserNotUniqueException;
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
     * @param AppUserEditRequest $userEditRequest
     *
     * @throws UserNotFoundException
     * @throws UserNotUniqueException
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
     * @param AppUserEditRequest $userEditRequest
     *
     * @return void
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     */
    public function sendMail(AppUserEditRequest $userEditRequest): void
    {
        $this->appUserMailService->sendAppUserEditMail(AppUserMail::TYPE_USER_EDIT_REQUEST, $userEditRequest);
    }

}
