<?php
/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */

namespace OswisOrg\OswisCoreBundle\Controller\Website;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomepageWebController extends AbstractController
{
    /**
     * @param int $page
     *
     * @return Response
     * @throws LogicException
     * @todo Refactor!!!!!
     */
    public function homepageAction(int $page = 0): Response
    {
        return $this->render('@OswisOrgOswisCore/web/pages/homepage.html.twig', ['page' => $page + 0]);
    }
}
