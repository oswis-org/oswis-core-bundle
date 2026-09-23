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
        );
        yield new MailParent(
            '@OswisOrgOswisCore/e-mail/pages/app-user-universal.html.twig',
            'Mail k uživatelskému účtu — podoba podle typu (ověření, heslo, změna údajů)',
        );
    }
}
