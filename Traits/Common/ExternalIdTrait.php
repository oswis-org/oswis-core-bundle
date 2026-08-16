<?php

/**
 * @noinspection PhpUnused
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Traits\Common;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping\Column;
use OswisOrg\OswisCoreBundle\Filter\SearchFilter;

trait ExternalIdTrait
{
    /**
     * External unique identifier.
     *
     * ⚠️ `string(64)`, ne `text` — na `longtext` nejde dát unikátní index bez prefixu, a právě
     * ten index je jediná spolehlivá pojistka proti duplicitám z importu (incident 2026-08-16:
     * import plateb vložil každý řádek dvakrát, protože deduplikace v kódu četla přes zastaralou
     * druhoúrovňovou cache). Nejdelší reálná hodnota má 35 znaků (ověřeno na produkci).
     * Trait používá jediná entita — {@see ParticipantPayment}.
     */
    #[Column(type: 'string', length: 64, nullable: true)]
    #[ApiFilter(SearchFilter::class, strategy: 'ipartial')]
    #[ApiFilter(OrderFilter::class)]
    #[ApiFilter(ExistsFilter::class)]
    protected ?string $externalId = null;

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function setExternalId(?string $externalId): void
    {
        $this->externalId = $externalId;
    }
}
