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

    /** Sloupec `subject` záznamu mailu je VARCHAR(255). */
    public const int SUBJECT_MAX_LENGTH = 255;

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
     * Předmět mailu ze šablony: vlastní předmět šablony (Twig), jinak `$fallback` — dosavadní předmět,
     * který skládá odesílající služba (název šablony + akce). Jediné místo, kde se o tom rozhoduje,
     * pro maily k účtu i k přihláškám (23. 9. 2026).
     *
     * Visící oddělovač na konci se ořízne: „Infomail – {{ akce }}" u přihlášky bez akce dá „Infomail",
     * ne „Infomail –" (dosavadní přípona se v tom případě taky nepřidala). Délka je omezená sloupcem
     * předmětu v záznamu mailu.
     *
     * @param array<string, mixed> $context
     */
    public function renderTemplateSubject(?string $subjectTwig, array $context, string $fallback): string
    {
        if (null === $subjectTwig || '' === trim($subjectTwig)) {
            return $fallback;
        }
        $subject = (string) preg_replace('/[\s\x{2013}\x{2014}\-:,|]+$/u', '', $this->renderSubject($subjectTwig, $context));

        return mb_substr('' === $subject ? $fallback : $subject, 0, self::SUBJECT_MAX_LENGTH);
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
