<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Filter;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class SearchAnnotation
{
    /** @var list<string> */
    public array $fields;

    /**
     * @param list<string> $fields
     */
    public function __construct(array $fields = [])
    {
        $this->fields = array_values(array_filter($fields, 'is_string'));
    }
}
