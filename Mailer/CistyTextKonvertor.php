<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mailer;

use Symfony\Component\Mime\HtmlToTextConverter\HtmlToTextConverterInterface;

/**
 * Textová alternativa mailu nesmí obsahovat obsah `<script>` a `<style>`.
 *
 * Symfony si text/plain část generuje z HTML převodníkem z league/html-to-markdown, který
 * nezná `<script>` ani `<style>` a jejich obsah bere jako text. U shrnutí přihlášky, které
 * kvůli zobrazení rezervace v Gmailu nese blok `application/ld+json`, tak textová část
 * začínala hned za oslovením syrovým JSONem (~500 z 5 100 znaků). Ověřovací mail ten blok
 * nemá, a proto byl čistý — což je přesně ten rozdíl mezi dvěma jinak stejnými maily.
 *
 * Kdo si mail zobrazí jako prostý text, uvidí uprostřed dopisu kus kódu; a filtrům nesedí,
 * když textová část neodpovídá tomu, co je v HTML vidět.
 *
 * ⚠️ Blok `ld+json` se z HTML NEODSTRAŇUJE — tam patří, Gmail podle něj zobrazuje rezervaci.
 * Řeší se jen jeho propsání do textové části.
 */
final readonly class CistyTextKonvertor implements HtmlToTextConverterInterface
{
    public function __construct(private HtmlToTextConverterInterface $puvodni)
    {
    }

    public function convert(string $html, string $charset): string
    {
        $ocisteny = preg_replace('#<(script|style)\b[^>]*>.*?</\1\s*>#is', '', $html);

        return $this->puvodni->convert($ocisteny ?? $html, $charset);
    }
}
