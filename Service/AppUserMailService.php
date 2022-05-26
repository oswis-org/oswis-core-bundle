<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEdit;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEditRequest;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserEditMail;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMail;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMailCategory;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMailGroup;
use OswisOrg\OswisCoreBundle\Exceptions\NotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Interfaces\Mail\MailCategoryInterface;
use OswisOrg\OswisCoreBundle\Repository\AppUserMailCategoryRepository;
use OswisOrg\OswisCoreBundle\Repository\AppUserMailGroupRepository;
use OswisOrg\OswisCoreBundle\Repository\AppUserMailRepository;

class AppUserMailService
{
    public function __construct(
        protected MailService $mailService,
        protected EntityManagerInterface $em,
        protected AppUserMailGroupRepository $groupRepository,
        protected AppUserMailCategoryRepository $categoryRepository,
        protected AppUserMailRepository $appUserMailRepository
    ) {
    }

    /**
     * @param  AppUser  $appUser
     * @param  string  $type
     * @param  AppUserToken|null  $appUserToken
     *
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotFoundException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\OswisException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException
     */
    public function sendAppUserMail(AppUser $appUser, string $type, ?AppUserToken $appUserToken = null): void
    {
        $isIS = false;
        if (null !== $appUserToken && $appUserToken->getAppUser() !== $appUser) {
            throw new OswisException('Token není kompatibilní s uživatelem.');
        }
        if (null === ($category = $this->getCategoryByType($type))) {
            throw new NotImplementedException($type, 'u uživatelských účtů');
        }
        if (null === ($group = $this->getGroup($appUser, $category)) || null === ($twigTemplate = $group->getTwigTemplate())) {
            throw new NotFoundException('Šablona e-mailu nebyla nalezena.');
        }
        $title = $twigTemplate->getName() ?? 'Změna u uživatelského účtu';
        $appUserEMail = new AppUserMail($appUser, $title, $type, $appUserToken);
        $data = [
            'appUser'      => $appUser,
            'category'     => $category,
            'type'         => $type,
            'appUserToken' => $appUserToken,
            'isIS'         => $isIS,
        ];
        $appUserEMail->setPastMails($this->appUserMailRepository->findByAppUser($appUser));
        $this->em->persist($appUserEMail);
        $this->em->flush();
        $templateName = $twigTemplate->getTemplateName() ?? '@OswisOrgOswisCore/e-mail/pages/app-user-universal.html.twig';
        $this->mailService->sendEMail($appUserEMail, $templateName, $data);
        $this->em->flush();
    }

    public function getCategoryByType(?string $type): ?AppUserMailCategory
    {
        return $type ? $this->categoryRepository->findByType($type) : null;
    }

    public function getGroup(AppUser $appUser, MailCategoryInterface $category): ?AppUserMailGroup
    {
        return $this->groupRepository->findByUser($appUser, $category);
    }

    /**
     * @param  string  $type
     * @param  \OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEditRequest|null  $userEditRequest
     * @param  \OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserEdit|null  $userEdit
     *
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotFoundException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException
     * @throws \OswisOrg\OswisCoreBundle\Exceptions\OswisException
     */
    public function sendAppUserEditMail(
        string $type,
        ?AppUserEditRequest $userEditRequest = null,
        ?AppUserEdit $userEdit = null,
    ): void {
        $appUser = $userEditRequest?->getAppUser();
        if (null === $appUser) {
            throw new OswisException('Token není platný.');
        }
        if (null === ($category = $this->getCategoryByType($type))) {
            throw new NotImplementedException($type, 'u uživatelských účtů');
        }
        if (null === ($group = $this->getGroup($appUser, $category)) || null === ($twigTemplate = $group->getTwigTemplate())) {
            throw new NotFoundException('Šablona e-mailu nebyla nalezena.');
        }
        $title = $twigTemplate->getName() ?? 'Změna u uživatelského účtu';
        $appUserEMail = new AppUserEditMail($title, $type, $userEditRequest, $userEdit);
        $data = [
            'appUser'         => $appUser,
            'category'        => $category,
            'type'            => $type,
            'userEditRequest' => $userEditRequest,
            'userEdit'        => $userEdit,
        ];
        $appUserEMail->setPastMails($this->appUserMailRepository->findByAppUser($appUser));
        $this->em->persist($appUserEMail);
        $this->em->flush();
        $templateName = $twigTemplate->getTemplateName() ?? '@OswisOrgOswisCore/e-mail/pages/app-user-universal.html.twig';
        $this->mailService->sendEMail($appUserEMail, $templateName, $data);
        $this->em->flush();
    }

}
