<?php
/**
 * Created by PhpStorm.
 * User: zakj
 * Date: 17.1.19
 * Time: 18:28
 */

namespace ZakJakub\OswisResourcesBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ZakjakubOswisResourcesBundleExtension extends Extension
{

    /**
     * Loads a specific configuration.
     *
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    final public function load(array $configs, ContainerBuilder $container): void
    {
        // TODO: Implement load() method.
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}