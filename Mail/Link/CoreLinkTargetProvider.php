<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Link;

use OswisOrg\OswisCoreBundle\Repository\Web\WebRedirectRepository;

/**
 * Cíle odkazů, které patří přímo core: portál (aplikace) a zkrácené odkazy (Přesměrování).
 *
 * Nic konkrétního pro jeden spolek — obojí je součást systému. Turnusy a přihlášky dodává kalendář.
 */
final readonly class CoreLinkTargetProvider implements MailLinkTargetProviderInterface
{
    public function __construct(private WebRedirectRepository $redirects)
    {
    }

    public function getLinkTargets(): iterable
    {
        // JEN kořen portálu, bez výběru stránky: `/portal/{foo}` přesměrovává na adresu aplikace
        // a cestu přitom ZAHODÍ (`PortalWebController`), takže „Přehled" i „Mapa" by skončily stejně.
        // Odkaz přímo na stránku v portálu vyžaduje předání cesty — to patří k práci na portálu.
        yield new MailLinkTarget(
            key: 'portal',
            label: 'Portál účastníka (aplikace)',
            group: 'Portál',
            route: 'oswis_org_oswis_core_portal',
            hint: 'Otevře aplikaci; po přihlášení tam každý vidí svoje.',
        );

        yield new MailLinkTarget(
            key: 'zkraceny',
            label: 'Zkrácený odkaz',
            group: 'Portál',
            route: 'oswis_org_oswis_core_redirect_short',
            parameter: 'slug',
            options: $this->redirectOptions(),
            hint: 'Cíl zkráceného odkazu jde změnit i potom, co e-mail odejde.',
        );

        yield new MailLinkTarget(
            key: 'ochrana-udaju',
            label: 'Ochrana osobních údajů',
            group: 'Web',
            route: 'oswis_org_oswis_core_gdpr_action',
        );
    }

    /** @return list<array{value: string, label: string}> */
    private function redirectOptions(): array
    {
        $options = [];
        foreach ($this->redirects->findBy(['deletedAt' => null], ['slug' => 'ASC'], 200) as $redirect) {
            $slug = $redirect->getSlug();
            if ('' === $slug) {
                continue;
            }
            $name = $redirect->getName();
            $options[] = ['value' => $slug, 'label' => '' === $name ? $slug : sprintf('%s (/r/%s)', $name, $slug)];
        }

        return $options;
    }
}
