<?php

declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle;

use OswisOrg\OswisCoreBundle\DependencyInjection\CompilerPass\RssExtenderPass;
use OswisOrg\OswisCoreBundle\DependencyInjection\CompilerPass\SiteMapExtenderPass;
use OswisOrg\OswisCoreBundle\DependencyInjection\CompilerPass\WebMenuExtenderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OswisOrgOswisCoreBundle extends Bundle
{
    final public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new SiteMapExtenderPass());
        $container->addCompilerPass(new RssExtenderPass());
        $container->addCompilerPass(new WebMenuExtenderPass());
    }
}
