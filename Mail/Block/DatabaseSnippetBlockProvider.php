<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Mail\Block;

use OswisOrg\OswisCoreBundle\Entity\TwigTemplate\TwigTemplate;
use OswisOrg\OswisCoreBundle\Repository\TwigTemplateRepository;

/** Bloky, které si spravuje tým — šablony druhu `snippet` v DB (klíč = slug). */
final class DatabaseSnippetBlockProvider implements MailBlockProviderInterface
{
    public function __construct(private readonly TwigTemplateRepository $repository)
    {
    }

    public function getBlocks(): iterable
    {
        foreach ($this->repository->findBy(['kind' => TwigTemplate::KIND_SNIPPET], ['name' => 'ASC']) as $template) {
            if (!$template instanceof TwigTemplate) {
                continue;
            }
            $slug = $template->getSlug();
            if ('' === $slug || $template->isRegular()) {
                continue;
            }
            yield new MailBlock($slug, $template->getName() ?? $slug, $slug);
        }
    }
}
