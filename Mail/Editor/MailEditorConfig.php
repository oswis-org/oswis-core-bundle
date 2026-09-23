<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Editor;

use OswisOrg\OswisCoreBundle\Mail\Block\MailBlockRegistry;
use OswisOrg\OswisCoreBundle\Mail\Catalog\MailCatalog;
use OswisOrg\OswisCoreBundle\Mail\Catalog\MailCatalogItem;
use OswisOrg\OswisCoreBundle\Mail\Link\MailLinkResolver;
use OswisOrg\OswisCoreBundle\Mail\Link\MailLinkTargetRegistry;
use OswisOrg\OswisCoreBundle\Mail\Markup\MailMarkupPolicy;
use OswisOrg\OswisCoreBundle\Mail\Parent\MailParentRegistry;
use OswisOrg\OswisCoreBundle\Mail\Rendering\MailRenderingException;

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
        private readonly MailLinkTargetRegistry $linkTargets,
        private readonly MailLinkResolver $linkResolver,
        private readonly MailParentRegistry $parents,
    ) {
    }

    /**
     * @return array{
     *     variables: list<array{group: string, label: string, expression: string, mayBeEmpty: bool, chip: string}>,
     *     variableGroups: array<string, list<array{group: string, label: string, expression: string, mayBeEmpty: bool, chip: string}>>,
     *     conditions: list<array{group: string, label: string, expression: string}>,
     *     blocks: list<array{key: string, label: string, template: string}>,
     *     classes: list<string>,
     *     alignments: list<string>,
     *     linkSchemes: list<string>,
     *     linkTargets: list<array<string, mixed>>,
     *     linkTargetGroups: array<string, list<array<string, mixed>>>,
     *     regionLabels: array<string, string>,
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
        // Nabídka proměnných po skupinách — Twig filtr pro seskupení nemá.
        $variableGroups = [];
        foreach ($variables as $variable) {
            $variableGroups[$variable['group']][] = $variable;
        }
        $blocks = [];
        foreach ($this->blocks->all() as $block) {
            $blocks[] = ['key' => $block->key, 'label' => $block->label, 'template' => $block->template];
        }

        $linkTargets = $this->linkTargetsWithUrls();
        // Cíle po skupinách kvůli `optgroup` v nabídce — Twig filtr pro seskupení nemá.
        $linkTargetGroups = [];
        foreach ($linkTargets as $target) {
            $group = $target['group'];
            $linkTargetGroups[is_string($group) ? $group : ''][] = $target;
        }

        return [
            'variables'      => $variables,
            'variableGroups' => $variableGroups,
            'conditions'     => $conditions,
            'blocks'         => $blocks,
            'classes'        => self::EDITOR_CLASSES,
            'alignments'     => self::ALIGNMENTS,
            'linkSchemes'    => MailMarkupPolicy::LINK_SCHEMES,
            'linkTargets'    => $linkTargets,
            'linkTargetGroups' => $linkTargetGroups,
            // Popisky úseků šablony („Tělo e-mailu" místo `{% block content_inner %}`) z obálek.
            'regionLabels'   => $this->parents->popiskyBloku(),
        ];
    }

    /**
     * Cíle odkazů i s hotovou adresou — dialog tak rovnou ukáže, kam odkaz povede. Cíl, u kterého
     * adresa nejde složit (smazaná stránka, změněná routa), se z nabídky vynechá: kvůli jedné
     * položce nemá editor přestat fungovat.
     *
     * @return list<array<string, mixed>>
     */
    private function linkTargetsWithUrls(): array
    {
        $targets = [];
        foreach ($this->linkTargets->all() as $target) {
            $item = $target->toArray();
            if (null === $target->parameter) {
                try {
                    $item['url'] = $this->linkResolver->url($target->key);
                } catch (MailRenderingException) {
                    continue;
                }
            } else {
                $options = [];
                foreach ($target->options as $option) {
                    try {
                        $options[] = [...$option, 'url' => $this->linkResolver->url($target->key, $option['value'])];
                    } catch (MailRenderingException) {
                        continue;
                    }
                }
                $item['options'] = $options;
            }
            $targets[] = $item;
        }

        return $targets;
    }
}
