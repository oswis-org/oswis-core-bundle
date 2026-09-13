<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Rendering;

use OswisOrg\OswisCoreBundle\Mail\Markup\MailMarkupPolicy;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;
use Symfony\Component\HtmlSanitizer\Visitor\AttributeSanitizer\AttributeSanitizerInterface;

/** `class` jen z {@see MailMarkupPolicy::CLASSES}, `style` jen povolené vlastnosti bez url()/expression(). */
final class MailStyleClassAttributeSanitizer implements AttributeSanitizerInterface
{
    public function getSupportedElements(): ?array
    {
        return null;
    }

    /** @return list<string> */
    public function getSupportedAttributes(): array
    {
        return ['style', 'class'];
    }

    public function sanitizeAttribute(string $element, string $attribute, string $value, HtmlSanitizerConfig $config): ?string
    {
        if ('class' === $attribute) {
            $classes = MailMarkupPolicy::splitClasses($value)['kept'];

            return [] === $classes ? null : implode(' ', $classes);
        }
        $declarations = MailMarkupPolicy::splitStyle($value)['kept'];

        return [] === $declarations ? null : implode('; ', $declarations).';';
    }
}
