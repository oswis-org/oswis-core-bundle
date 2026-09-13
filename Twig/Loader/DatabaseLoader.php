<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Twig\Loader;

use OswisOrg\OswisCoreBundle\Entity\TwigTemplate\TwigTemplate;
use OswisOrg\OswisCoreBundle\Repository\TwigTemplateRepository;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class DatabaseLoader implements LoaderInterface
{
    public function __construct(protected TwigTemplateRepository $repository)
    {
    }

    final public function getSourceContext(string $name): Source
    {
        $row = $this->repository->findLoaderRowBySlug($name);
        if (null === $row) {
            throw new LoaderError(sprintf('Template "%s" does not exist in TwigTemplateRepository.', $name));
        }
        if (null !== $row['regularTemplateName']) {
            throw new LoaderError(sprintf(
                'Template "%s" is only reference to regular template "%s".',
                $name,
                $row['regularTemplateName'],
            ));
        }

        return new Source($row['textValue'] ?? '', $name);
    }

    final public function getTemplate(string $name): ?TwigTemplate
    {
        return $this->repository->findBySlug($name);
    }

    final public function exists(string $name): bool
    {
        $row = $this->repository->findLoaderRowBySlug($name);

        return null !== $row && null === $row['regularTemplateName'];
    }

    /**
     * Klíč = název + id + otisk OBSAHU.
     *
     * PROČ: Twig pojmenuje třídu zkompilované šablony podle tohoto klíče
     * (`Environment::getTemplateClass`). Na produkci je `auto_reload` vypnutý, `isFresh()` se tedy
     * nevolá — s klíčem jen podle názvu se po úpravě textu brala stará zkompilovaná verze až do
     * dalšího nasazení (nalezeno 13. 9. 2026). Otisk obsahu (ne `updatedAt`) funguje i pro dvě
     * úpravy v téže sekundě a pro změnu mimo ORM.
     */
    final public function getCacheKey(string $name): string
    {
        $row = $this->repository->findLoaderRowBySlug($name);
        if (null === $row) {
            throw new LoaderError(sprintf('Template "%s" does not exist in TwigTemplateRepository.', $name));
        }

        return $name.'#'.$row['id'].'#'.hash('xxh3', $row['textValue'] ?? '');
    }

    /** Každá změna obsahu = nový klíč = nová třída; při zapnutém `auto_reload` stačí existence. */
    final public function isFresh(string $name, int $time): bool
    {
        return null !== $this->repository->findLoaderRowBySlug($name);
    }
}