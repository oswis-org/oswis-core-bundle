<?php /** @noinspection PhpUnused */

namespace Zakjakub\OswisCoreBundle\Controller\Website;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class IndexWebController extends AbstractController
{
    /**
     * @return Response
     * @throws LogicException
     */
    /** @noinspection MethodShouldBeFinalInspection */
    public function indexAction(): Response
    {
        return $this->render('@ZakjakubOswisCore/web/pages/homepage.html.twig');
    }
}
