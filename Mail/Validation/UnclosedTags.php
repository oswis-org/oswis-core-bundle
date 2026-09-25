<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Validation;

/**
 * HTML značka bez zavírací `>` (např. `</a<br>`) ve zdroji mailu.
 *
 * PROČ: prohlížeč i poštovní klient takovou značku „spolknou" i s tou následující — z `</a<br>`
 * zmizí zalomení řádku a odkaz zůstane otevřený, takže se dva řádky slijí do jednoho (kampaň #36,
 * 25. 9. 2026, ruční úprava v režimu kódu). Twig ani kontrola vykresleného HTML to nepoznají:
 * parser chybu tiše „opraví". Proto se hledá přímo ve zdroji.
 *
 * Twig, komentáře a obsah `<style>`/`<script>` se předem zamaskují (smí v nich být `<` i bez značky).
 */
final class UnclosedTags
{
    /**
     * @return list<array{tag: string, line: int, excerpt: string}> značka (např. `</a`), řádek zdroje a úryvek kolem ní
     */
    public static function find(string $source): array
    {
        $masked = preg_replace_callback(
            '~\{\{.*?\}\}|\{%.*?%\}|\{#.*?#\}|<!--.*?-->|<(style|script)\b[^>]*>.*?</\1\s*>~si',
            static fn (array $match): string => str_repeat('x', strlen($match[0])),
            $source,
        ) ?? $source;
        // Hodnota atributu v uvozovkách smí `<` i `>` obsahovat (např. `title="…<br>…"` u bublin).
        preg_match_all('~<(/?[a-zA-Z][a-zA-Z0-9:-]*)(?:[^<>"\']|"[^"]*"|\'[^\']*\')*+(?=<|\z)~', $masked, $matches, PREG_OFFSET_CAPTURE);
        $found = [];
        foreach ($matches[0] as $index => [$match, $offset]) {
            $start = max(0, $offset - 20);
            // mb_strcut = bajtové pozice, ale nerozsekne znak (za značkou bývá emoji).
            $excerpt = mb_strcut($source, $start, $offset - $start + strlen($match) + 12, 'UTF-8');
            $found[] = [
                'tag' => '<'.$matches[1][$index][0],
                'line' => substr_count($source, "\n", 0, $offset) + 1,
                'excerpt' => ($start > 0 ? '…' : '').trim((string) preg_replace('~\s+~u', ' ', $excerpt)).'…',
            ];
        }

        return $found;
    }

    /** Víc hlášek najednou nepomůže — rozbitý kus (např. neuzavřené uvozovky) jich umí vyrobit stovky. */
    private const int MAX_MESSAGES = 5;

    /** @return list<string> česká hláška pro každou poškozenou značku (chyba, která brání uložení) */
    public static function messages(string $source): array
    {
        $found = self::find($source);
        $messages = array_map(
            // „Chyba v zápisu" jako u Twigu: editor s ní nepustí z režimu kódu zpět — převod do editoru
            // by značku „opravil" po svém (odkaz by pohltil další text) a chyba by zmizela z očí.
            static fn (array $item): string => sprintf('Chyba v zápisu (řádek %d): značka „%s" nemá na konci „>" (%s), prohlížeč by spolkl i to, co za ní následuje, a text by se rozsypal', $item['line'], $item['tag'], $item['excerpt']),
            array_slice($found, 0, self::MAX_MESSAGES),
        );
        if (count($found) > self::MAX_MESSAGES) {
            $messages[] = sprintf('Dalších takových značek: %d.', count($found) - self::MAX_MESSAGES);
        }

        return $messages;
    }
}
