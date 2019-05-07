<?php

namespace Zakjakub\OswisCoreBundle\DependencyInjection;

use RuntimeException;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @return TreeBuilder
     * @throws RuntimeException
     */
    final public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('zakjakub_oswis_core');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->info('Default configuration for core module of OSWIS (One Simple Web IS).')
            ->children()
            ->booleanNode('dummy_parameter_boolean')
            ->defaultFalse()
            ->info('Dummy parameter for testing (boolean).')
            ->end()
            ->integerNode('dummy_parameter_integer')
            ->defaultValue(3)
            ->info('Dummy parameter for testing (integer).')
            ->end()
            ->end();

        return $treeBuilder;
    }

}