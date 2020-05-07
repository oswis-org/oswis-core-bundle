<?php

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

class Publicity
{
    public ?bool $publicOnWeb = null;

    public function __construct(?bool $publicOnWeb = null)
    {
        $this->publicOnWeb = $publicOnWeb;
    }
}
