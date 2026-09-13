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
            $classes = array_values(array_filter(
                preg_split('/\s+/', trim($value)) ?: [],
                static fn (string $class): bool => MailMarkupPolicy::isAllowedClass($class),
            ));

            return [] === $classes ? null : implode(' ', $classes);
        }

        $kept = [];
        foreach (explode(';', $value) as $declaration) {
            [$property, $cssValue] = array_pad(explode(':', $declaration, 2), 2, '');
            $property = strtolower(trim($property));
            $cssValue = trim($cssValue);
            if ('' === $property || '' === $cssValue || !MailMarkupPolicy::isAllowedCssProperty($property)) {
                continue;
            }
            if (1 === preg_match('/url\s*\(|expression\s*\(|javascript:|[<>"\\\\]/i', $cssValue)) {
                continue;
            }
            $kept[] = $property.': '.$cssValue;
        }

        return [] === $kept ? null : implode('; ', $kept).';';
    }
}
