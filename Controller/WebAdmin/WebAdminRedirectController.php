<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\WebAdmin;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use OswisOrg\OswisCoreBundle\Entity\Web\WebRedirect;
use OswisOrg\OswisCoreBundle\Form\WebAdmin\WebRedirectType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Web admin CRUD over {@see WebRedirect} short links (/web_admin/presmerovani).
 * List + create + edit + soft-delete/restore; every mutation is POST + CSRF.
 */
#[IsGranted('ROLE_ADMIN')]
class WebAdminRedirectController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function index(): Response
    {
        $redirects = $this->em->getRepository(WebRedirect::class)->findBy([], ['slug' => 'ASC']);

        return $this->render('@OswisOrgOswisCore/web_admin/redirects/index.html.twig', [
            'redirects'  => $redirects,
            'pageTitle'  => 'Přesměrování (krátké odkazy)',
            'page_title' => 'Přesměrování :: ADMIN',
        ]);
    }

    public function new(Request $request): Response
    {
        $redirect = new WebRedirect();
        $form = $this->createForm(WebRedirectType::class, $redirect);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($redirect);
            $this->em->flush();
            $this->addFlash('success', sprintf('Přesměrování „%s" vytvořeno.', $redirect->getSlug()));

            return new RedirectResponse($this->generateUrl('oswis_org_oswis_core_web_admin_redirects'));
        }

        return $this->render('@OswisOrgOswisCore/web_admin/redirects/edit.html.twig', [
            'form'       => $form,
            'entity'     => $redirect,
            'pageTitle'  => 'Nové přesměrování',
            'page_title' => 'Nové přesměrování :: ADMIN',
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $redirect = $this->em->find(WebRedirect::class, $id)
            ?? throw $this->createNotFoundException('Přesměrování nenalezeno.');
        $form = $this->createForm(WebRedirectType::class, $redirect);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', sprintf('Přesměrování „%s" uloženo.', $redirect->getSlug()));

            return new RedirectResponse($this->generateUrl('oswis_org_oswis_core_web_admin_redirects'));
        }

        return $this->render('@OswisOrgOswisCore/web_admin/redirects/edit.html.twig', [
            'form'       => $form,
            'entity'     => $redirect,
            'pageTitle'  => sprintf('Přesměrování: %s', $redirect->getSlug()),
            'page_title' => sprintf('Přesměrování: %s :: ADMIN', $redirect->getSlug()),
        ]);
    }

    /** Soft-delete: the public route stops resolving the slug (404); the row stays restorable. */
    public function delete(Request $request, int $id): Response
    {
        $redirect = $this->em->find(WebRedirect::class, $id)
            ?? throw $this->createNotFoundException('Přesměrování nenalezeno.');
        if (!$this->isCsrfTokenValid('web_redirect_delete_'.$id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }
        $redirect->setDeletedAt(new DateTime());
        $this->em->flush();
        $this->addFlash('success', sprintf('Přesměrování „%s" deaktivováno (lze obnovit).', $redirect->getSlug()));

        return new RedirectResponse($this->generateUrl('oswis_org_oswis_core_web_admin_redirects'));
    }

    public function restore(Request $request, int $id): Response
    {
        $redirect = $this->em->find(WebRedirect::class, $id)
            ?? throw $this->createNotFoundException('Přesměrování nenalezeno.');
        if (!$this->isCsrfTokenValid('web_redirect_restore_'.$id, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Neplatný CSRF token.');
        }
        $redirect->setDeletedAt(null);
        $this->em->flush();
        $this->addFlash('success', sprintf('Přesměrování „%s" obnoveno.', $redirect->getSlug()));

        return new RedirectResponse($this->generateUrl('oswis_org_oswis_core_web_admin_redirects'));
    }
}
