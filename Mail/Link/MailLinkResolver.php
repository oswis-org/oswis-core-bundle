<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Link;

use OswisOrg\OswisCoreBundle\Mail\Rendering\MailRenderingException;
use Symfony\Component\Routing\Exception\ExceptionInterface as RoutingException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Z klíče cíle složí ABSOLUTNÍ adresu — `odkaz('akce', 'seznamovak-2027-1')`.
 *
 * Absolutní schválně: v e-mailu relativní adresa nefunguje. Z příkazové řádky (cron) bere doménu
 * `router.default_uri` — jinak by v mailech z cronu vznikly odkazy na `localhost`
 * (chyba z června 2026, viz `reference_cli_urls_need_default_uri`).
 */
final readonly class MailLinkResolver
{
    public function __construct(
        private MailLinkTargetRegistry $targets,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @throws MailRenderingException když cíl neexistuje nebo z parametrů nejde složit adresa —
     *                                lépe hlasitě při kontrole než tiše odeslat rozbitý odkaz
     */
    public function url(string $key, string|int|null $value = null): string
    {
        $target = $this->targets->get($key);
        if (null === $target) {
            throw new MailRenderingException(sprintf('Odkaz „%s" v nabídce není — zkontroluj, že je napsaný správně.', $key));
        }
        $parameters = $target->fixedParams;
        if (null !== $target->parameter) {
            if (null === $value || '' === (string) $value) {
                throw new MailRenderingException(sprintf('Odkaz „%s" potřebuje vybrat, kam vede.', $target->label));
            }
            $parameters[$target->parameter] = $value;
        }

        try {
            return $this->urlGenerator->generate($target->route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (RoutingException $exception) {
            throw new MailRenderingException(
                sprintf('Adresu odkazu „%s" nejde složit: %s', $target->label, $exception->getMessage()),
                previous: $exception,
            );
        }
    }
}
