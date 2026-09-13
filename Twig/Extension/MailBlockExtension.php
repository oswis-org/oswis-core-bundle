<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Twig\Extension;

use OswisOrg\OswisCoreBundle\Mail\Rendering\MailBlockRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/** `{{ blok('rekapitulace-prihlasky') }}` — zástupná značka; obsah dosadí MailBlockRenderer. */
final class MailBlockExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [new TwigFunction('blok', MailBlockRenderer::placeholder(...), ['is_safe' => ['html']])];
    }
}
