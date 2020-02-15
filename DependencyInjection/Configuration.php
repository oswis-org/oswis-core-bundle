<?php
/**
 * @noinspection PhpUnusedPrivateMethodInspection
 * @noinspection MethodShouldBeFinalInspection
 * @noinspection PhpUnused
 */

namespace Zakjakub\OswisCoreBundle\DependencyInjection;

use RuntimeException;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @noinspection ClassNameCollisionInspection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @throws RuntimeException
     */
    final public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('zakjakub_oswis_core', 'array');
        $rootNode = $treeBuilder->getRootNode();
        assert($rootNode instanceof ArrayNodeDefinition);
        $rootNode->info('Default configuration for core module of OSWIS (One Simple Web IS).');
        $this->addGeneralConfig($rootNode);
        $this->addEmailConfig($rootNode);
        $this->addAdminConfig($rootNode);
        $this->addWebConfig($rootNode);

        return $treeBuilder;
    }

    private function addGeneralConfig(ArrayNodeDefinition $rootNode): void
    {
        $rootNode->children()->arrayNode('app')->info('General settings.')->addDefaultsIfNotSet()->children()->scalarNode('name')->info('Name of application.')->defaultValue(
            'OSWIS'
        )->example('John\'s IS')->end()->scalarNode('url')->info('Base URL of app.')->defaultValue('https://oswis.org')->example('https://oswis.org')->end()->scalarNode(
            'name_short'
        )->info(
            'Shortened name of application.'
        )->defaultValue('OSWIS')->example('JIS')->end()->scalarNode('name_long')->info('Long (full) name of application.')->defaultValue('One Simple Web IS')->example(
            'John\'s personal information system'
        )->end()->scalarNode('description')->info('Description of application.')->defaultValue('Simple modular information system based on ApiPlatform.')->example(
            'Personal information system used by John Doe for information management.'
        )->end()->scalarNode('logo')->defaultValue('@images/logo.png')->info('Path to app logo.')->example(
            ['@images/logo.png', '../assets/assets/images/logo.png']
        )->end()->end()->end()->end();
    }

    private function addEmailConfig(NodeDefinition $rootNode): void
    {
        $rootNode->children() /// System e-mails settings.
        ->arrayNode('email')->info('Sender of system e-mails.')->addDefaultsIfNotSet()->children()->scalarNode('address')->info('E-mail address of sender.')->defaultValue(
            'oswis@oswis.org'
        )->example('john.doe@example.com')->end()->scalarNode('name')->info('Name of sender.')->defaultValue('OSWIS')->example('John Doe')->end()->scalarNode(
            'reply_path'
        )->info(
            'Return-Path address.'
        )->defaultValue(null)->example('info@oswis.org')->end()->scalarNode('return_path')->info('Address for reply.')->defaultValue(null)->example('webmaster@oswis.org')->end(
        )->scalarNode(
            'archive_address'
        )->info('Address for archivation.')->defaultValue(null)->example('archive@oswis.org')->end()->scalarNode('archive_name')->info('Name of archive.')->defaultValue(
            null
        )->example(
            'OSWIS Archive'
        )->end()->scalarNode('default_subject')->info('Default subject for messages without set subject.')->defaultValue('System message')->example('Message')->end()->scalarNode(
            'logo'
        )->info(
            'Path of logo.'
        )->defaultValue('../public/img/web/logo-whitebg.png')->example('../public/img/web/logo-whitebg.png')->end()->end()->end();
    }

    private function addAdminConfig(NodeDefinition $rootNode): void
    {
        $rootNode->children()->arrayNode('admin')->info('Info about main administrator.')->addDefaultsIfNotSet()->children()->scalarNode('name')->info(
            'Name of main admin.'
        )->defaultValue(null)->example('John Doe')->end()->scalarNode('email')->info('E-mail address of administrator.')->defaultValue(null)->example('admin@oswis.org')->end(
        )->scalarNode('web')->info('Website of administrator.')->defaultValue(null)->example('https://oswis.org')->end()->scalarNode('phone')->info(
            'Phone of administrator.'
        )->defaultValue(null)->example('+000 000 000 000')->end()->end()->end();
    }

    private function addWebConfig(NodeDefinition $rootNode): void
    {
        $rootNode->children() /// Web settings.
        ->arrayNode('web')->info('Website settings.')->addDefaultsIfNotSet()->children()->scalarNode('title')->info('Title of website.')->defaultValue(
            'One Simple Web IS'
        )->example(
            'John\'s personal website'
        )->end()->scalarNode('url')->info('Base URL of website.')->defaultValue('https://oswis.org')->example('https://oswis.org')->end()->scalarNode('title_short')->info(
            'Shortened title of website.'
        )->defaultValue('OSWIS')->example('John\'s web')->end()->scalarNode('title_long')->info('Long (full) title of application.')->defaultValue(
            'One Simple Web IS'
        )->example(
            'John\'s personal website'
        )->end()->scalarNode('description')->info('Description of website.')->defaultValue('Simple modular information system based on ApiPlatform.')->example(
            'Personal website of John Doe. Contains some info.'
        )->end()->scalarNode('geo_x')->info('Geo coordinates - x.')->defaultValue('49.5923997')->example('49.5923997')->end()->scalarNode('geo_y')->info(
            'Geo coordinates - y.'
        )->defaultValue('17.2635003')->example('17.2635003')->end()->scalarNode('color')->info('Theme color.')->defaultValue(
            '#006FAD'
        )->example('#006FAD')->end()->scalarNode('logo')->defaultValue('@images/logo.png')->info('Path to app logo.')->example(
            ['@images/logo.png', '../assets/assets/images/logo.png']
        )->end()->scalarNode('social_image')->defaultValue('@images/logo.png')->info('Path to app logo.')->example(
            ['@images/logo.png', '../assets/assets/images/logo.png']
        )->end()->scalarNode('author')->info('Author of application.')->defaultValue('Jakub Zak (https://jakubzak.cz)')->example('Jakub Zak (https://jakubzak.cz)')->end(
        )->scalarNode(
            'copyright'
        )->info('Copyright owner of application.')->defaultValue('Jakub Zak (https://jakubzak.cz)')->example('Jakub Zak (https://jakubzak.cz)')->end()->end()->end();
    }
}
