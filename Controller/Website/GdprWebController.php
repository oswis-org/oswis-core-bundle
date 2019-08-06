<?php

namespace Zakjakub\OswisCoreBundle\Controller\Website;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class GdprWebController extends AbstractController
{

    /**
     * @return Response
     * @throws LogicException
     */
    final public function gdprAction(): Response
    {
        return $this->render(
            '@ZakjakubOswisCore/web/pages/gdpr.html.twig'
        );
    }

}
