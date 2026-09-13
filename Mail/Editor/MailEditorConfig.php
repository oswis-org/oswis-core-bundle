<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Editor;

use OswisOrg\OswisCoreBundle\Mail\Block\MailBlockRegistry;
use OswisOrg\OswisCoreBundle\Mail\Catalog\MailCatalog;
use OswisOrg\OswisCoreBundle\Mail\Catalog\MailCatalogItem;
use OswisOrg\OswisCoreBundle\Mail\Markup\MailMarkupPolicy;

/**
 * Nastavení editoru textu mailu — katalog proměnných a podmínek, vložené bloky a pravidla čištění
 * z JEDINÉHO místa (spec 2026-09-13 §4.1: editor pravidla dostává jako hodnotu, nevymýšlí je).
 * Tvar pole čte TypeScript `assets/mail-editor/config.ts` v aplikaci.
 */
final class MailEditorConfig
{
    /** Zarovnání, která editor nabízí (slovník §4.3); `right` čištění pustí, v panelu není. */
    public const array ALIGNMENTS = ['left', 'center', 'justify'];

    /** Třídy v panelu editoru; `token-box` zůstává jen kvůli starým kampaním. */
    public const array EDITOR_CLASSES = ['warning', 'highlight'];

    public function __construct(
        private readonly MailCatalog $catalog,
        private readonly MailBlockRegistry $blocks,
    ) {
    }

    /**
     * @return array{
     *     variables: list<array{group: string, label: string, expression: string, mayBeEmpty: bool, chip: string}>,
     *     conditions: list<array{group: string, label: string, expression: string}>,
     *     blocks: list<array{key: string, label: string, template: string}>,
     *     classes: list<string>,
     *     alignments: list<string>,
     *     linkSchemes: list<string>,
     * }
     */
    public function toArray(): array
    {
        $variables = [];
        $conditions = [];
        foreach ($this->catalog->items() as $item) {
            if (MailCatalogItem::CONDITION === $item->kind) {
                $conditions[] = ['group' => $item->group, 'label' => $item->label, 'expression' => $item->expression];
            } else {
                $variables[] = [
                    'group'      => $item->group,
                    'label'      => $item->label,
                    'expression' => $item->expression,
                    'mayBeEmpty' => $item->mayBeEmpty,
                    'chip'       => $item->chip ?? $item->label,
                ];
            }
        }
        $blocks = [];
        foreach ($this->blocks->all() as $block) {
            $blocks[] = ['key' => $block->key, 'label' => $block->label, 'template' => $block->template];
        }

        return [
            'variables'   => $variables,
            'conditions'  => $conditions,
            'blocks'      => $blocks,
            'classes'     => self::EDITOR_CLASSES,
            'alignments'  => self::ALIGNMENTS,
            'linkSchemes' => MailMarkupPolicy::LINK_SCHEMES,
        ];
    }
}
