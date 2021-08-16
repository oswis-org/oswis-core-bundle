<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomepageWebController extends AbstractController
{
    /**
     * @param  int  $page
     *
     * @return Response
     * @todo Refactor!!!!!
     */
    public function homepageAction(int $page = 0): Response
    {
        return $this->render('@OswisOrgOswisCore/web/pages/homepage.html.twig', ['page' => $page + 0]);
    }
}
