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
            /// App general.
            ->arrayNode('app')
            ->info('General settings.')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('name')
            ->info('Name of application.')
            ->defaultValue('OSWIS')
            ->example('John\'s IS')
            ->end()
            ->scalarNode('name_short')
            ->info('Shortened name of application.')
            ->defaultValue('OSWIS')
            ->example('JIS')
            ->end()
            ->scalarNode('name_long')
            ->info('Long (full) name of application.')
            ->defaultValue('One Simple Web IS')
            ->example('John\'s personal information system')
            ->end()
            ->scalarNode('description')
            ->info('Description of application.')
            ->defaultValue('Simple modular information system based on ApiPlatform.')
            ->example('Personal information system used by John Doe for information management.')
            ->end()
            ->scalarNode('logo')
            ->defaultValue('@ZakjakubOswisCore/Resources/public/logo.png')
            ->info('Path to app logo.')
            ->example(['@ZakjakubOswisCore/Resources/public/logo.png', '../assets/assets/images/logo.png'])
            ->end()
            ->end()
            ->end()
            /// Admin info.
            ->arrayNode('admin')
            ->info('Info about main administrator.')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('name')
            ->info('Name of main admin.')
            ->defaultValue(null)
            ->example('John Doe')
            ->end()
            ->scalarNode('email')
            ->info('E-mail address of administrator.')
            ->defaultValue(null)
            ->example('admin@oswis.org')
            ->end()
            ->scalarNode('web')
            ->info('Website of administrator.')
            ->defaultValue(null)
            ->example('https://oswis.org')
            ->end()
            ->scalarNode('phone')
            ->info('Phone of administrator.')
            ->defaultValue(null)
            ->example('+000 000 000 000')
            ->end()
            ->end()
            ->end()
            /// System e-mails settings.
            ->arrayNode('email')
            ->info('Sender of system e-mails.')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('address')
            ->info('E-mail address of sender.')
            ->defaultValue('oswis@oswis.org')
            ->example('john.doe@example.com')
            ->end()
            ->scalarNode('name')
            ->info('Name of sender.')
            ->defaultValue('OSWIS')
            ->example('John Doe')
            ->end()
            ->scalarNode('reply_path')
            ->info('Return-Path address.')
            ->defaultValue('oswis@oswis.org')
            ->example('info@oswis.org')
            ->end()
            ->scalarNode('return_path')
            ->info('Address for reply.')
            ->defaultValue('oswis@oswis.org')
            ->example('webmaster@oswis.org')
            ->end()
            ->scalarNode('default_subject')
            ->info('Default subject for messages without set subject.')
            ->defaultValue('System message')
            ->example('Message')
            ->end()
            ->end()
            ->end()
            /// App general.
            ->arrayNode('web')
            ->info('Website settings.')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('title')
            ->info('Title of website.')
            ->defaultValue('One Simple Web IS')
            ->example('John\'s personal website')
            ->end()
            ->scalarNode('title_short')
            ->info('Shortened title of website.')
            ->defaultValue('OSWIS')
            ->example('John\'s web')
            ->end()
            ->scalarNode('name_long')
            ->info('Long (full) title of application.')
            ->defaultValue('One Simple Web IS')
            ->example('John\'s personal website')
            ->end()
            ->scalarNode('description')
            ->info('Description of website.')
            ->defaultValue('Simple modular information system based on ApiPlatform.')
            ->example('Personal website of John Doe. Contains some info.')
            ->end()
            ->scalarNode('logo')
            ->defaultValue('@ZakjakubOswisCore/Resources/public/logo.png')
            ->info('Path to app logo.')
            ->example(['@ZakjakubOswisCore/Resources/public/logo.png', '../assets/assets/images/logo.png'])
            ->end()
            ->scalarNode('social_image')
            ->defaultValue('@ZakjakubOswisCore/Resources/public/logo.png')
            ->info('Path to app logo.')
            ->example(['@ZakjakubOswisCore/Resources/public/logo.png', '../assets/assets/images/logo.png'])
            ->end()
            ->end()
            ->end()
            // end
            ->end();

        return $treeBuilder;
    }

}