<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Twig\Extension;

use OswisOrg\OswisCoreBundle\Mail\Link\MailLinkResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * `{{ odkaz('akce', 'seznamovak-2027-1') }}` — adresa stránky, kterou máme, složená až při odeslání.
 *
 * Dvojče k `{{ blok('…') }}`: v uloženém textu zůstává čitelný zápis, který editor pozná a umí ho
 * v dialogu zase nabídnout jako vybraný cíl.
 */
final class MailLinkExtension extends AbstractExtension
{
    public const string FUNCTION_NAME = 'odkaz';

    public function __construct(private readonly MailLinkResolver $resolver)
    {
    }

    public function getFunctions(): array
    {
        return [new TwigFunction(self::FUNCTION_NAME, $this->resolver->url(...))];
    }
}
