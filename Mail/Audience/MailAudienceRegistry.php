<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Audience;

/** Údaje stavebnice podmínky ze všech poskytovatelů (při shodě klíče vyhrává dřívější). */
final class MailAudienceRegistry
{
    /** @var array<string, MailAudienceField>|null */
    private ?array $fields = null;

    /** @param iterable<MailAudienceProviderInterface> $providers */
    public function __construct(private readonly iterable $providers)
    {
    }

    /** @return list<MailAudienceField> */
    public function all(): array
    {
        if (null === $this->fields) {
            $this->fields = [];
            foreach ($this->providers as $provider) {
                foreach ($provider->getFields() as $field) {
                    $this->fields[$field->key] ??= $field;
                }
            }
        }

        return array_values($this->fields);
    }
}
