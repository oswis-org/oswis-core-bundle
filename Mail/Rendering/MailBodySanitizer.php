<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Rendering;

use OswisOrg\OswisCoreBundle\Mail\Markup\MailMarkupPolicy;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerAction;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * Čištění vykresleného textu mailu podle {@see MailMarkupPolicy}. Neznámá značka zmizí, ale její
 * text zůstane (akce Block); nebezpečné značky zmizí i s obsahem. Co se zahodí, ohlásí MailValidator.
 */
final class MailBodySanitizer
{
    private ?HtmlSanitizer $sanitizer = null;

    public function sanitize(string $html): string
    {
        if (strlen($html) > MailMarkupPolicy::MAX_BODY_BYTES) {
            throw new MailRenderingException(sprintf(
                'Text mailu je příliš dlouhý (%d kB, nejvýš %d kB).',
                intdiv(strlen($html), 1000),
                intdiv(MailMarkupPolicy::MAX_BODY_BYTES, 1000),
            ));
        }

        return ($this->sanitizer ??= $this->build())->sanitize($html);
    }

    private function build(): HtmlSanitizer
    {
        $config = (new HtmlSanitizerConfig())
            ->defaultAction(HtmlSanitizerAction::Block)
            ->allowLinkSchemes(MailMarkupPolicy::LINK_SCHEMES)
            ->allowMediaSchemes(MailMarkupPolicy::MEDIA_SCHEMES)
            ->allowRelativeLinks(false)
            ->allowRelativeMedias(false)
            ->withAttributeSanitizer(new MailStyleClassAttributeSanitizer())
            ->withMaxInputLength(MailMarkupPolicy::MAX_BODY_BYTES);
        foreach (MailMarkupPolicy::DROPPED_ELEMENTS as $element) {
            $config = $config->dropElement($element);
        }
        foreach (MailMarkupPolicy::allowedElements() as $element => $attributes) {
            $config = $config->allowElement($element, $attributes);
        }

        return new HtmlSanitizer($config);
    }
}
