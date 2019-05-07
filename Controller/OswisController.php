<?php


namespace Zakjakub\OswisCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Zakjakub\OswisCoreBundle\Provider\OswisCoreSettingsProvider;

class OswisController extends AbstractController
{

    private $oswisCoreSettingsProvider;

    public function __construct(OswisCoreSettingsProvider $oswisCoreSettingsProvider)
    {
        $this->oswisCoreSettingsProvider = $oswisCoreSettingsProvider;
    }

    final public function index(): Response
    {
        return $this->json(
            [
                'dummyParameterInteger' => $this->oswisCoreSettingsProvider->dummyParameterInteger,
                'dummyParameterBoolean' => $this->oswisCoreSettingsProvider->dummyParameterBoolean,
            ]
        );
    }


}