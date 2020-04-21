<?php

namespace OswisOrg\OswisCoreBundle\Entity;

class Publicity
{
    public ?bool $publicOnWeb = null;

    public ?bool $publicOnWebRoute = null;

    public ?bool $publicInIS = null;

    public ?bool $publicInPortal = null;

    public function __construct(?bool $publicOnWeb = null, ?bool $publicOnWebRoute = null, ?bool $publicInIS = null, ?bool $publicInPortal = null)
    {
        $this->publicOnWeb = $publicOnWeb;
        $this->publicOnWebRoute = $publicOnWebRoute;
        $this->publicInIS = $publicInIS;
        $this->publicInPortal = $publicInPortal;
    }
}
