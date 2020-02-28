<?php

namespace Zakjakub\OswisCoreBundle\Entity\Web;

class Breadcrumb
{
    public ?string $url = null;

    public ?string $title = null;

    public function __construct(
        ?string $url = null,
        ?string $title = null
    ) {
        $this->url = $url;
        $this->title = $title;
    }
}
