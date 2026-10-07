<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\WebAdmin;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\MailAttachment\MailAttachment;
use OswisOrg\OswisCoreBundle\Mail\Attachment\MailAttachmentException;
use OswisOrg\OswisCoreBundle\Mail\Attachment\MailAttachmentStore;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Přílohy zpráv v administraci (dávka 3.5): nahrání z formuláře zprávy (JSON) a stažení správcem.
 * Veřejný odkaz pro příjemce obsluhuje {@see \OswisOrg\OswisCoreBundle\Controller\Web\MailAttachmentDownloadController}.
 */
#[IsGranted('ROLE_ADMIN')]
final class MailAttachmentController extends AbstractController
{
    public const string CSRF_ID = 'mail_attachment';

    public function __construct(
        private readonly MailAttachmentStore $store,
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface $logger,
    ) {
    }

    /** Odpověď `{id, name, size, sizeLabel, mime}`, nebo `{error}` s českou hláškou. */
    public function upload(Request $request): JsonResponse
    {
        if (!$this->isCsrfTokenValid(self::CSRF_ID, $request->request->getString('_token'))) {
            return new JsonResponse(['error' => 'Platnost stránky vypršela — načti ji znovu.'], Response::HTTP_FORBIDDEN);
        }
        $file = $request->files->get('file');
        if (!$file instanceof UploadedFile) {
            return new JsonResponse(['error' => 'Nebyl vybrán žádný soubor.'], Response::HTTP_BAD_REQUEST);
        }
        try {
            $attachment = $this->store->store($file, $this->getUser()?->getUserIdentifier());
        } catch (MailAttachmentException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $this->em->persist($attachment);
        $this->em->flush();
        $this->logger->info(sprintf('Příloha zprávy nahrána: #%d %s (%s, %s).', $attachment->getId() ?? 0, $attachment->getOriginalName(), $attachment->getMimeType(), $attachment->getUploadedBy() ?? '?'));

        return new JsonResponse(self::popis($attachment));
    }

    public function download(int $id): Response
    {
        $attachment = $this->em->find(MailAttachment::class, $id);
        if (!$attachment instanceof MailAttachment || !is_file($this->store->absolutePath($attachment))) {
            throw $this->createNotFoundException('Příloha nenalezena.');
        }

        return self::soubor($this->store->absolutePath($attachment), $attachment);
    }

    /** @return array{id: int, name: string, size: int, sizeLabel: string, mime: string} */
    public static function popis(MailAttachment $attachment): array
    {
        return [
            'id'        => $attachment->getId() ?? 0,
            'name'      => $attachment->getOriginalName(),
            'size'      => $attachment->getSize(),
            'sizeLabel' => $attachment->getSizeLabel(),
            'mime'      => $attachment->getMimeType(),
        ];
    }

    /** Soubor ke stažení (vždy jako příloha, ne zobrazit v prohlížeči — HTML/SVG se nahrát nedá, ale jistota). */
    public static function soubor(string $path, MailAttachment $attachment): BinaryFileResponse
    {
        $response = new BinaryFileResponse($path, Response::HTTP_OK, ['Content-Type' => $attachment->getMimeType(), 'X-Content-Type-Options' => 'nosniff']);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $attachment->getOriginalName(), self::ascii($attachment->getOriginalName()));
        $response->setPrivate();

        return $response;
    }

    private static function ascii(string $name): string
    {
        $ascii = (string) preg_replace('/[^\x20-\x7E]/', '_', (string) (iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name) ?: $name));

        return str_replace(['%', '/', '\\'], '_', $ascii);
    }
}
