<?php

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

use DateTime;

class RssItem
{
    public ?string $path = null;

    public ?string $title = null;

    public ?string $textValue = null;

    public ?DateTime $dateTime = null;

    public function __construct(?string $path, ?string $title = null, ?DateTime $dateTime = null, ?string $textValue = null)
    {
        $this->path = $path;
        $this->dateTime = $dateTime ?? new DateTime();
        $this->title = $title;
        $this->textValue = $textValue;
    }
}
