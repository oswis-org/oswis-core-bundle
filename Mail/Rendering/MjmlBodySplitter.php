<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Rendering;

/**
 * Rozdělí vyčištěné tělo na sousední `mj-text` / `mj-button` / `mj-image` / `mj-divider`
 * (spec 2026-09-13 §3.3 krok 5). `mj-text` je v MJML „ending tag" — MJML komponenta uvnitř by
 * v mailu skončila jako neklikací text. Samostatně se vytáhnou jen komponenty na nejvyšší úrovni;
 * zanořené (např. v seznamu) zůstanou a ohlásí je MailValidator.
 */
final class MjmlBodySplitter
{
    /** @var list<string> */
    public const array BLOCK_COMPONENTS = ['mj-button', 'mj-image', 'mj-divider'];

    public function split(string $html): string
    {
        if ('' === trim($html)) {
            return '';
        }
        $document = \Dom\HTMLDocument::createFromString('<!DOCTYPE html><html><body>'.$html.'</body></html>', LIBXML_NOERROR);
        $body = $document->body;
        if (null === $body) {
            return self::textRun($html);
        }
        $out = '';
        $run = '';
        foreach (iterator_to_array($body->childNodes) as $node) {
            if ($node instanceof \Dom\Element && in_array(strtolower($node->localName), self::BLOCK_COMPONENTS, true)) {
                $out .= self::textRun($run);
                $run = '';
                $out .= $document->saveHtml($node);
                continue;
            }
            $run .= $document->saveHtml($node);
        }

        return $out.self::textRun($run);
    }

    private static function textRun(string $html): string
    {
        return '' === trim($html) ? '' : '<mj-text>'.$html.'</mj-text>';
    }
}
