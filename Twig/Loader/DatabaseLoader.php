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
        if (null === ($template = $this->getTemplate($name))) {
            throw new LoaderError(sprintf('Template "%s" does not exist in TwigTemplateRepository.', $name));
        }
        if ($template->isRegular()) {
            throw new LoaderError(sprintf('Template "%s" is only reference to regular template "%s".', $name, $template->getRegularTemplateName()));
        }

        return new Source(''.$template->getTextValue(), $name);
    }

    final public function getTemplate(string $name): ?TwigTemplate
    {
        return $this->repository->findBySlug($name);
    }

    final public function exists(string $name): bool
    {
        $template = $this->getTemplate($name);

        return $template && !$template->isRegular();
    }

    final public function getCacheKey(string $name): string
    {
        return $name;
    }

    final public function isFresh(string $name, int $time): bool
    {
        if (null === ($template = $this->getTemplate($name))) {
            return false;
        }

        return $template->isFresh($time);
    }
}