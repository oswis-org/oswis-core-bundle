<?php

/**
 * @noinspection MethodShouldBeFinalInspection
 */
declare(strict_types=1);

namespace OswisOrg\OswisCoreBundle\DependencyInjection\CompilerPass;

use OswisOrg\OswisCoreBundle\Service\Web\RssService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

class RssExtenderPass implements CompilerPassInterface
{
    /**
     * @param  ContainerBuilder  $container
     *
     * @throws InvalidArgumentException
     * @throws ServiceNotFoundException
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(RssService::class)) { // always first check if the primary service is defined
            return;
        }
        $definition = $container->findDefinition(RssService::class);
        $taggedServices
            = $container->findTaggedServiceIds('oswis.rss_extender'); // find all service IDs with the app.mail_transport tag
        foreach ($taggedServices as $id => $tags) { // add the transport service to the TransportChain service
            $definition->addMethodCall('addExtender', [new Reference($id)]);
        }
    }
}