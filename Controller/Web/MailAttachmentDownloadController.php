<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\Web;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Controller\WebAdmin\MailAttachmentController;
use OswisOrg\OswisCoreBundle\Entity\MailAttachment\MailAttachment;
use OswisOrg\OswisCoreBundle\Mail\Attachment\MailAttachmentStore;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Veřejný odkaz ke stažení přílohy z mailu (dávka 3.5): `/priloha/{token}` — bez přihlášení, jen pro soubory,
 * které už odkazem odešly ({@see MailAttachment::isPublic()}). Neznámý i nezveřejněný token = stejná 404,
 * ať adresa neprozradí, že soubor existuje.
 */
final class MailAttachmentDownloadController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly MailAttachmentStore $store,
    ) {
    }

    public function download(string $token): Response
    {
        $attachment = 1 === preg_match('/^[0-9a-f]{64}$/', $token)
            ? $this->em->getRepository(MailAttachment::class)->findOneBy(['token' => $token])
            : null;
        if (!$attachment instanceof MailAttachment || !$attachment->isPublic() || !is_file($this->store->absolutePath($attachment))) {
            throw $this->createNotFoundException('Soubor nenalezen.');
        }
        $response = MailAttachmentController::soubor($this->store->absolutePath($attachment), $attachment);
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
