<?php
/**
 * @noinspection PhpUnused
 */

namespace OswisOrg\OswisCoreBundle\Controller\Website;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class InternalActionsWebController extends AbstractController
{
    /**
     * @param Request $request
     *
     * @return Response
     * @throws AccessDeniedHttpException
     * @throws IOException
     * @throws LogicException
     */
    final public function clearCache(Request $request): Response
    {
        self::checkIP($request);
        $filesystem = new Filesystem();
        $filesystem->remove('../var/cache');

        return $this->render(
            '@OswisOrgOswisCore/web/pages/message.html.twig',
            ['title' => 'OK', 'message' => 'Akce úspěšně provedena.']
        );
    }

    /**
     * @param Request $request
     *
     * @throws AccessDeniedHttpException
     */
    public static function checkIP(Request $request): void
    {
        $allowedIPs = ['127.0.0.1', '::1', '93.93.35.182'];
        if (!IpUtils::checkIp($request->getClientIp(), $allowedIPs)) {
            throw new AccessDeniedHttpException('Nedostatečná oprávnění.');
        }
    }
}
