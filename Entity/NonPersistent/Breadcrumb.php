<?php

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

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
