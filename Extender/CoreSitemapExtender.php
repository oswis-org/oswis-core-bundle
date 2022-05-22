<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\Extender;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use OswisOrg\OswisCoreBundle\Entity\NonPersistent\SiteMapItem;
use OswisOrg\OswisCoreBundle\Interfaces\Web\SiteMapExtenderInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CoreSitemapExtender implements SiteMapExtenderInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getItems(): Collection
    {
        try {
            return new ArrayCollection([
                new SiteMapItem($this->urlGenerator->generate('oswis_org_oswis_core_homepage_action'), SiteMapItem::CHANGE_FREQUENCY_DAILY, null, 1.000),
                new SiteMapItem($this->urlGenerator->generate('oswis_org_oswis_core_gdpr_action')),
                new SiteMapItem($this->urlGenerator->generate('oswis_org_oswis_core_robots_txt')),
                new SiteMapItem($this->urlGenerator->generate('oswis_org_oswis_core_portal')),
                new SiteMapItem($this->urlGenerator->generate('oswis_org_oswis_core_admin')),
            ]);
        } catch (MissingMandatoryParametersException|InvalidParameterException|RouteNotFoundException $e) {
            return new ArrayCollection();
        }
    }
}