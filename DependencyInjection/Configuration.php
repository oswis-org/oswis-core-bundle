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
            ->children()
            ->booleanNode('dummy_parameter_boolean')
            ->defaultFalse()
            ->info('Dummy parameter for testing (boolean, deafult is false).')
            ->end()
            ->integerNode('dummy_parameter_integer')
            ->defaultValue(3)
            ->info('Dummy parameter for testing (integer, deafult is 3).')
            ->end()
            ->end();

        return $treeBuilder;
    }

}