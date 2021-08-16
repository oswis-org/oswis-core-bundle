<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Entity\NonPersistent;

use DateTime;
use OswisOrg\OswisCoreBundle\Interfaces\Common\NameableInterface;

class SiteMapItem
{
    public const CHANGE_FREQUENCY_DAILY = 'daily';
    public const CHANGE_FREQUENCY_WEEKLY = 'weekly';

    public ?string $path = null;

    public ?float $priority = null;

    public ?string $changeFrequency = null;

    public ?DateTime $lastChangeAt = null;

    public ?NameableInterface $entity = null;

    public function __construct(?string $path, ?string $changeFrequency = null, ?DateTime $lastChangeAt = null, ?float $priority = null, ?NameableInterface $entity = null)
    {
        $this->path = $path ?? null;
        $this->lastChangeAt = $lastChangeAt ?? new DateTime();
        $this->priority = $priority ?? 0.900;
        $this->changeFrequency = $changeFrequency ?? 'weekly';
        $this->entity = $entity;
    }
}
