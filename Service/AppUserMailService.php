<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserToken;
use OswisOrg\OswisCoreBundle\Entity\AppUserEMail\AppUserEMail;
use OswisOrg\OswisCoreBundle\Entity\AppUserEMail\AppUserEMailCategory;
use OswisOrg\OswisCoreBundle\Entity\AppUserEMail\AppUserEMailGroup;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\Nameable;
use OswisOrg\OswisCoreBundle\Exceptions\InvalidTypeException;
use OswisOrg\OswisCoreBundle\Exceptions\NotFoundException;
use OswisOrg\OswisCoreBundle\Exceptions\NotImplementedException;
use OswisOrg\OswisCoreBundle\Exceptions\OswisException;
use OswisOrg\OswisCoreBundle\Interfaces\EMail\EMailCategoryInterface;
use OswisOrg\OswisCoreBundle\Repository\AppUserEMailCategoryRepository;
use OswisOrg\OswisCoreBundle\Repository\AppUserEMailGroupRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Exception\LogicException as MimeLogicException;
use Twig\Environment;

class AppUserMailService extends AbstractMailService
{
    public const TYPE_PASSWORD_CHANGE = AppUserService::PASSWORD_CHANGE;
    public const TYPE_PASSWORD_CHANGE_REQUEST = AppUserService::PASSWORD_CHANGE_REQUEST;
    public const TYPE_ACTIVATION = AppUserService::ACTIVATION;
    public const TYPE_ACTIVATION_REQUEST = AppUserService::ACTIVATION_REQUEST;

    protected AppUserEMailGroupRepository $groupRepository;

    protected AppUserEMailCategoryRepository $categoryRepository;

    protected Environment $twig;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        MailerInterface $mailer,
        AppUserEMailGroupRepository $groupRepository,
        AppUserEMailCategoryRepository $categoryRepository,
        Environment $twig
    ) {
        parent::__construct($em, $logger, $mailer);
        $this->groupRepository = $groupRepository;
        $this->categoryRepository = $categoryRepository;
        $this->twig = $twig;
    }

    /**
     * @param AppUser           $appUser
     * @param string            $type
     * @param AppUserToken|null $appUserToken
     *
     * @throws TransportExceptionInterface|MimeLogicException
     * @throws OswisException|NotFoundException|InvalidTypeException|NotImplementedException
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
        $appUserEMail = new AppUserEMail($appUser, new Nameable($title), $appUser->getEmail(), $type, $appUserToken);
        $data = [
            'appUser'      => $appUser,
            'category'     => $category,
            'type'         => $type,
            'appUserToken' => $appUserToken,
            'isIS'         => $isIS,
        ];
        $this->em->persist($appUserEMail);
        $templateName = $twigTemplate->getTemplateName() ?? '@OswisOrgOswisCore/e-mail/pages/app-user-universal.html.twig';
        try {
            $this->sendEMail($appUserEMail, $templateName, $data, ''.$appUser->getName());
        } catch (TransportExceptionInterface|MimeLogicException $exception) {
            $this->logger->error('App user e-mail exception: '.$exception->getMessage());
            $appUserEMail->setInternalNote($exception->getMessage());
            $this->em->flush();
            throw $exception;
        }
        $this->em->flush();
    }

    public function getCategoryByType(?string $type): ?AppUserEMailCategory
    {
        return $this->categoryRepository->findByType($type);
    }

    public function getGroup(AppUser $appUser, EMailCategoryInterface $category): ?AppUserEMailGroup
    {
        return $this->groupRepository->findByUser($appUser, $category);
    }

}
