<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMail;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMailCategory;
use OswisOrg\OswisCoreBundle\Entity\AppUserMail\AppUserMailGroup;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Interfaces\Mail\MailCategoryInterface;
use OswisOrg\OswisCoreBundle\Repository\AppUserMailCategoryRepository;
use OswisOrg\OswisCoreBundle\Repository\AppUserMailGroupRepository;

class AppUserMailService
{
    protected MailService $mailService;

    protected EntityManagerInterface $em;

    protected AppUserMailGroupRepository $groupRepository;

    protected AppUserMailCategoryRepository $categoryRepository;

    public function __construct(
        MailService $mailService,
        EntityManagerInterface $em,
        AppUserMailGroupRepository $groupRepository,
        AppUserMailCategoryRepository $categoryRepository
    ) {
        $this->mailService = $mailService;
        $this->em = $em;
        $this->groupRepository = $groupRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param AppUser           $appUser
     * @param string            $type
     * @param AppUserToken|null $appUserToken
     *
     * @throws OswisException|NotFoundException|NotImplementedException|InvalidTypeException
     */
    public function sendAppUserEMail(AppUser $appUser, string $type, ?AppUserToken $appUserToken = null): void
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
        $this->em->persist($appUserEMail);
        $templateName = $twigTemplate->getTemplateName() ?? '@OswisOrgOswisCore/e-mail/pages/app-user-universal.html.twig';
        $this->mailService->sendEMail($appUserEMail, $templateName, $data);
        $this->em->flush();
    }

    public function getCategoryByType(?string $type): ?AppUserMailCategory
    {
        return $this->categoryRepository->findByType($type);
    }

    public function getGroup(AppUser $appUser, MailCategoryInterface $category): ?AppUserMailGroup
    {
        return $this->groupRepository->findByUser($appUser, $category);
    }

}
