<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\WebAdmin;

use OswisOrg\OswisCoreBundle\Mail\Image\MailImageException;
use OswisOrg\OswisCoreBundle\Mail\Image\MailImageStore;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Nahrání obrázku do textu mailu z editoru (dialog „Obrázek" i vložení ze schránky; spec §4.5, dávka 2b).
 * Odpověď JSON: `{url, width, height}` — adresa je ÚPLNÁ (https://…), jinak by se obrázek v mailu nenačetl.
 */
#[IsGranted('ROLE_ADMIN')]
final class MailImageController extends AbstractController
{
    public function __construct(
        private readonly MailImageStore $store,
        private readonly UrlHelper $urlHelper,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function upload(Request $request): JsonResponse
    {
        if (!$this->isCsrfTokenValid('mail_image', (string) $request->request->get('_token'))) {
            return new JsonResponse(['error' => 'Platnost stránky vypršela — načti ji znovu.'], Response::HTTP_FORBIDDEN);
        }
        $file = $request->files->get('image');
        if (!$file instanceof UploadedFile) {
            return new JsonResponse(['error' => 'Nebyl vybrán žádný obrázek.'], Response::HTTP_BAD_REQUEST);
        }
        try {
            $image = $this->store->store($file);
        } catch (MailImageException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $this->logger->info(sprintf('Obrázek do mailu nahrán: %s (%s).', $image['path'], $this->getUser()?->getUserIdentifier() ?? '?'));

        return new JsonResponse([
            'url'    => $this->urlHelper->getAbsoluteUrl($image['path']),
            'width'  => $image['width'],
            'height' => $image['height'],
        ]);
    }
}
