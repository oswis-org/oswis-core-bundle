<?php

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

use DateTime;

class SiteMapItem
{
    public ?string $url = null;

    public ?int $priority = null;

    public ?string $changeFrequency = null;

    public ?DateTime $lastChangeAt = null;

    public function __construct(?string $url, ?DateTime $lastChangeAt, ?int $priority, ?string $changeFrequency)
    {
        $this->url = $url ?? null;
        $this->lastChangeAt = $lastChangeAt ?? new DateTime();
        $this->priority = $priority ?? 70;
        $this->changeFrequency = $changeFrequency ?? null;
    }
}
