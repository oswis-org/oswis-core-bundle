<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\DependencyInjection\CompilerPass;

use OswisOrg\OswisCoreBundle\Mailer\CistyTextKonvertor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Podstrčí `BodyRendereru` převodník, který z textové alternativy vyhodí `<script>` a `<style>`.
 *
 * Proč compiler pass a ne YAML: `twig.mime_body_renderer` definuje Twig bundle jen s argumentem
 * `twig`; převodník je nepovinný třetí argument a v YAMLu se nedá doplnit jediný argument, aniž
 * by se celá definice přepsala (a přišlo se tím o `locale_switcher`, který tam dosazuje jiný
 * pass). Tady se jen dosadí chybějící argument a zbytek definice zůstane, jak je.
 */
final class CistyTextMailuPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig.mime_body_renderer')
            || !$container->hasDefinition(CistyTextKonvertor::class)) {
            return;
        }
        $definice = $container->getDefinition('twig.mime_body_renderer');
        $argumenty = $definice->getArguments();
        // ⚠️ Argument 2 nejde dosadit, dokud není definován argument 1 — Twig bundle předává
        // jen argument 0 (`twig`) a zbytek nechává na výchozích hodnotách. Doplňujeme tedy
        // i prázdný `$context`, jinak kontejner skončí na „Argument 2 must be defined before".
        $argumenty[1] ??= [];
        $argumenty[2] = new Reference(CistyTextKonvertor::class);
        ksort($argumenty);
        $definice->setArguments($argumenty);
    }
}
