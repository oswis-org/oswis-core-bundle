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
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Exceptions\TokenInvalidException;
use OswisOrg\OswisCoreBundle\Interfaces\Mail\MailCategoryInterface;
use OswisOrg\OswisCoreBundle\Mail\Rendering\MailRenderer;
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
        protected AppUserMailRepository $appUserMailRepository,
        protected MailRenderer $mailRenderer,
    ) {
    }

    /**
     * Sends an account e-mail and returns its record. A transport failure is logged, not thrown, so callers
     * (e.g. activation during a registration) are not broken by a mail outage; use {@see AppUserMail::isSent()}
     * to find out whether it was delivered.
     *
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     * @throws TokenInvalidException
     */
    public function sendAppUserMail(AppUser $appUser, string $type, ?AppUserToken $appUserToken = null): AppUserMail
    {
        $isIS = false;
        if (null !== $appUserToken && $appUserToken->getAppUser() !== $appUser) {
            throw new OswisException('Token není kompatibilní s uživatelem.');
        }
        if (null === ($category = $this->getCategoryByType($type))) {
            throw new NotImplementedException($type, 'u uživatelských účtů');
        }
        if (null === ($group = $this->getGroup($appUser, $category))
            || null === ($twigTemplate
                = $group->getTwigTemplate())) {
            throw new NotFoundException('Šablona e-mailu nebyla nalezena.');
        }
        $data = [
            'appUser'      => $appUser,
            'category'     => $category,
            'type'         => $type,
            'appUserToken' => $appUserToken,
            'isIS'         => $isIS,
        ];
        $title = $this->mailRenderer->renderTemplateSubject(
            $twigTemplate->getSubject(),
            $data,
            $twigTemplate->getName() ?? 'Změna u uživatelského účtu',
        );
        $appUserEMail = new AppUserMail($appUser, $title, $type, $appUserToken);
        $appUserEMail->setPastMails($this->appUserMailRepository->findByAppUser($appUser));
        $this->em->persist($appUserEMail);
        $this->em->flush();
        $templateName = $twigTemplate->getTemplateName();
        $this->mailService->sendEMail($appUserEMail, $templateName, $data);
        $this->em->flush();

        return $appUserEMail;
    }

    /**
     * Account mail whose text is a FILE template — system flows with a fixed wording (the
     * "continue your registration" login link) have no admin-editable category in the database.
     *
     * Everything else stays the same as for database-driven mails: the copy is stored (without the
     * secrets), the delivery has a state and shows up in the person's history. Before 16. 9. 2026
     * this flow handed the message straight to the mailer, so nothing about it was ever recorded.
     *
     * @param array<string, mixed> $extraData
     *
     * @throws InvalidTypeException
     * @throws OswisException
     */
    public function sendFromFileTemplate(
        AppUser $appUser,
        string $type,
        string $template,
        string $subject,
        array $extraData = [],
        ?AppUserToken $appUserToken = null,
    ): AppUserMail {
        if (null !== $appUserToken && $appUserToken->getAppUser() !== $appUser) {
            throw new OswisException('Token není kompatibilní s uživatelem.');
        }
        $appUserEMail = new AppUserMail($appUser, $subject, $type, $appUserToken);
        $appUserEMail->setPastMails($this->appUserMailRepository->findByAppUser($appUser));
        $this->em->persist($appUserEMail);
        $this->em->flush();
        $this->mailService->sendEMail($appUserEMail, $template, array_merge([
            'appUser'      => $appUser,
            'category'     => null,
            'type'         => $type,
            'appUserToken' => $appUserToken,
            'isIS'         => false,
        ], $extraData));
        $this->em->flush();

        return $appUserEMail;
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
     * @param string                  $type
     * @param AppUserEditRequest|null $userEditRequest
     * @param AppUserEdit|null        $userEdit
     *
     * @throws InvalidTypeException
     * @throws NotFoundException
     * @throws NotImplementedException
     * @throws OswisException
     */
    public function sendAppUserEditMail(
        string $type,
        ?AppUserEditRequest $userEditRequest = null,
        ?AppUserEdit $userEdit = null,
    ): void
    {
        $appUser = $userEditRequest?->getAppUser();
        if (null === $appUser) {
            throw new OswisException('Token není platný.');
        }
        if (null === ($category = $this->getCategoryByType($type))) {
            throw new NotImplementedException($type, 'u uživatelských účtů');
        }
        if (null === ($group = $this->getGroup($appUser, $category))
            || null === ($twigTemplate
                = $group->getTwigTemplate())) {
            throw new NotFoundException('Šablona e-mailu nebyla nalezena.');
        }
        $data = [
            'appUser'         => $appUser,
            'category'        => $category,
            'type'            => $type,
            'userEditRequest' => $userEditRequest,
            'userEdit'        => $userEdit,
        ];
        $title = $this->mailRenderer->renderTemplateSubject(
            $twigTemplate->getSubject(),
            $data,
            $twigTemplate->getName() ?? 'Změna u uživatelského účtu',
        );
        $appUserEMail = new AppUserEditMail($title, $type, $userEditRequest, $userEdit);
        $appUserEMail->setPastMails($this->appUserMailRepository->findByAppUser($appUser));
        $this->em->persist($appUserEMail);
        $this->em->flush();
        $templateName = $twigTemplate->getTemplateName();
        $this->mailService->sendEMail($appUserEMail, $templateName, $data);
        $this->em->flush();
    }

}
