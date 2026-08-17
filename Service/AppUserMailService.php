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
     * Odešle e-mail k uživatelskému účtu a **vrátí ho, aby šlo zjistit, jestli opravdu odešel**.
     *
     * ⚠️ Selhání přenosu (SMTP) NEVYHAZUJE výjimku — jediný důkaz odeslání je sloupec `sent`
     * ({@see AppUserMail::isSent()}). Dokud tahle metoda vracela `void`, nemohl to volající
     * zjistit a stránky tvrdily „na e-mail byl odeslán odkaz" i tehdy, když se neodeslalo nic.
     * U obnovy hesla to znamená člověka, který marně čeká na mail a nemá se jak dostat dovnitř.
     *
     * Záměrně se NEVYHAZUJE výjimka ani tady: tuhle metodu volá i aktivace při registraci, a
     * výpadek SMTP nesmí shodit registraci (tatáž úvaha jako u
     * {@see \OswisOrg\OswisCalendarBundle\Service\Participant\ParticipantMailService::sendSummary()}).
     * Kdo potřebuje vědět, jak to dopadlo, ptá se na NÁVRATOVOU HODNOTU.
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
        $templateName = $twigTemplate->getTemplateName();
        $this->mailService->sendEMail($appUserEMail, $templateName, $data);
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
        $templateName = $twigTemplate->getTemplateName();
        $this->mailService->sendEMail($appUserEMail, $templateName, $data);
        $this->em->flush();
    }

}
