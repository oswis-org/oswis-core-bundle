<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Parent;

/** Obálky, které zná core: obecná zpráva a maily k uživatelskému účtu. */
final class CoreMailParentProvider implements MailParentProviderInterface
{
    public function getParents(): iterable
    {
        yield new MailParent(
            '@OswisOrgOswisCore/e-mail/pages/message.html.twig',
            'Obecná zpráva — hlavička s logem, oslovení, podpis a patička',
            [
                'html_title'           => 'Titulek e-mailu',
                'html_preview'         => 'Náhledový text ve schránce',
                'header_outer'         => 'Hlavička (celá)',
                'header_inner'         => 'Hlavička',
                'header_logo'          => 'Logo v hlavičce',
                'content_header_outer' => 'Oslovení (celé)',
                'content_header_inner' => 'Oslovení',
                'content_outer'        => 'Tělo e-mailu (celé)',
                'content_inner'        => 'Tělo e-mailu',
                'content_footer_outer' => 'Závěr (celý)',
                'content_footer_inner' => 'Závěr',
                'footer_outer'         => 'Patička pod čarou (celá)',
                'footer_inner'         => 'Patička pod čarou',
            ],
        );
        yield new MailParent(
            '@OswisOrgOswisCore/e-mail/pages/app-user-universal.html.twig',
            'Mail k uživatelskému účtu — podoba podle typu (ověření, heslo, změna údajů)',
        );
    }
}
