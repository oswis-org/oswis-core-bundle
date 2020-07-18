<?php
/**
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class WebBannerController extends AbstractController
{
    public function showWebBanner(
        ?string $type = null,
        ?string $identifier = null,
        ?string $subType = null,
        ?string $title = null,
        ?string $subTitle = null,
        ?string $link = null,
        ?string $image = null
    ): Response {
        return $this->render(
            '@OswisOrgOswisCore/web/parts/web-banner.html.twig',
            [
                'type'       => $type,
                'subType'    => $subType,
                'identifier' => $identifier,
                'title'      => $title,
                'subTitle'   => $subTitle,
                'link'       => $link,
                'image'      => $image,
            ]
        );
    }
}
