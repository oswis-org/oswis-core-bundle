<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\Web;

use OswisOrg\OswisCoreBundle\Entity\NonPersistent\WebMenuItem;
use OswisOrg\OswisCoreBundle\Service\Web\WebMenuService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class WebMenuController extends AbstractController
{
    protected WebMenuService $webMenuService;

    public function __construct(WebMenuService $webMenuService)
    {
        $this->webMenuService = $webMenuService;
    }

    public function showMenu(?string $menu = WebMenuItem::MAIN_MENU): Response
    {
        return $this->render('@OswisOrgOswisCore/web/parts/main-menu.html.twig', ['items' => $this->webMenuService->getItems($menu)]);
    }
}
