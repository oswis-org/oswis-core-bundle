<?php

namespace OswisOrg\OswisCoreBundle;

use OswisOrg\OswisCoreBundle\DependencyInjection\CompilerPass\RssExtenderPass;
use OswisOrg\OswisCoreBundle\DependencyInjection\CompilerPass\SiteMapExtenderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OswisOrgOswisCoreBundle extends Bundle
{
    final public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new SiteMapExtenderPass());
        $container->addCompilerPass(new RssExtenderPass());
    }
}
