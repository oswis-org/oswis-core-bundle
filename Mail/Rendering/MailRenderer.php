<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Rendering;

use Twig\Environment;

/**
 * Jedna vykreslovací cesta pro náhled, zkoušku, 1 i N příjemců, kampaně a kontrolu chyb
 * (spec 2026-09-13 §3.3): normalizace MJML značek → Twig s kontextem příjemce → čištění →
 * dosazení bloků → rozdělení na MJML → obal {@see self::WRAPPER_TEMPLATE} → MJML → HTML.
 */
final class MailRenderer
{
    public const string WRAPPER_TEMPLATE = '@OswisOrgOswisCore/e-mail/pages/body.html.twig';

    public function __construct(
        private readonly Environment $twig,
        private readonly MailBodySanitizer $sanitizer,
        private readonly MjmlBodySplitter $splitter,
        private readonly MailBlockRenderer $blocks,
    ) {
    }

    /**
     * Kroky 1–2: Twig s kontextem příjemce, BEZ čištění (pro kontrolu chyb).
     *
     * @param array<string, mixed> $context
     */
    public function renderTwig(string $bodyTwig, array $context): string
    {
        $rendered = $this->twig->createTemplate(MjmlTagNormalizer::normalize($bodyTwig))->render($context);

        return MjmlTagNormalizer::normalize($rendered);
    }

    /**
     * Kroky 1–5: MJML fragment pro obal. `$renderBlocks = false` nechá zástupné značky bloků
     * (kontrola se striktními proměnnými nesmí padat na benevolentním kódu bloků).
     *
     * @param array<string, mixed> $context
     */
    public function renderBody(string $bodyTwig, array $context, bool $renderBlocks = true): string
    {
        $html = $this->sanitizer->sanitize($this->renderTwig($bodyTwig, $context));
        if ($renderBlocks) {
            $html = $this->blocks->replacePlaceholders($html, $context);
        }

        return $this->splitter->split($html);
    }

    /**
     * Předmět jako Twig → jeden řádek prostého textu.
     *
     * @param array<string, mixed> $context
     */
    public function renderSubject(string $subjectTwig, array $context): string
    {
        $subject = str_contains($subjectTwig, '{') ? $this->twig->createTemplate($subjectTwig)->render($context) : $subjectTwig;
        $plain = html_entity_decode(strip_tags($subject), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', $plain));
    }

    /**
     * Celé HTML mailu — totéž, co odejde (náhled).
     *
     * @param array<string, mixed> $context
     */
    public function renderHtml(string $bodyTwig, array $context): string
    {
        return $this->twig->render(self::WRAPPER_TEMPLATE, array_merge($context, [
            'mjmlBody' => $this->renderBody($bodyTwig, $context),
        ]));
    }
}
