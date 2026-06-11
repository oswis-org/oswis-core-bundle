<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\Web;

use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Exceptions\NotFoundException;
use OswisOrg\OswisCoreBundle\Repository\Web\WebRedirectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public endpoint of admin-managed short links ({@see \OswisOrg\OswisCoreBundle\Entity\Web\WebRedirect}):
 * /redirect/{slug} and the QR-friendly alias /r/{slug}.
 */
class RedirectWebController extends AbstractController
{
    public function __construct(
        private readonly WebRedirectRepository $redirectRepository,
        private readonly EntityManagerInterface $em,
    ) {
    }

    /**
     * 302 (temporary — the target may be swapped in the admin at any time, and a reverse proxy or
     * browser must not cache it permanently) with explicit no-store. Unknown or soft-deleted slug
     * → standard 404 page.
     *
     * @throws NotFoundException
     */
    public function follow(string $slug): Response
    {
        $redirect = $this->redirectRepository->findOneActiveBySlug($slug);
        if (null === $redirect || '' === $redirect->getTargetUrl()) {
            throw new NotFoundException("(přesměrování: '$slug')");
        }
        $redirect->registerHit();
        $this->em->flush();

        $response = new RedirectResponse($redirect->getTargetUrl(), Response::HTTP_FOUND);
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }
}
