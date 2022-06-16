<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

class Publicity
{
    public ?bool $publicOnWeb = null;

    public ?bool $publicInApp = null;

    public function __construct(
        ?bool $publicOnWeb = null,
        ?bool $publicInApp = null,
    ) {
        $this->publicOnWeb = $publicOnWeb;
        $this->publicInApp = $publicInApp;
    }
}
