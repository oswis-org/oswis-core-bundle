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

            ->arrayNode('email_sender')
            ->info('Sender of system e-mails.')
            ->children()
            ->scalarNode('address')->info('E-mail address of sender.')->defaultValue('oswis@oswis.org')->end()
            ->scalarNode('name')->info('Name of sender.')->defaultValue('OSWIS')->end()
            ->end()

            ->end();

        return $treeBuilder;
    }

}