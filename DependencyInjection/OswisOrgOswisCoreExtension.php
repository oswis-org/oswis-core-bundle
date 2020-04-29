<?php

namespace OswisOrg\OswisCoreBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class OswisOrgOswisCoreExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Loads a specific configuration.
     *
     * @throws ServiceNotFoundException
     * @throws Exception
     */
    final public function load(array $configs, ContainerBuilder $container): void
    {
        $configs ??= [];
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
        $configuration = $this->getConfiguration($configs, $container);
        if ($configuration) {
            $config = $this->processConfiguration($configuration, $configs);
            $this->oswisCoreSettingsProvider($container, $config);
        }
    }

    /**
     * @throws ServiceNotFoundException
     */
    private function oswisCoreSettingsProvider(ContainerBuilder $container, array $config): void
    {
        $definition = $container->getDefinition('oswis_org_oswis_core.oswis_core_settings_provider');
        $definition->setArgument(0, $config['app']);
        $definition->setArgument(1, $config['admin']);
        $definition->setArgument(2, $config['email']);
        $definition->setArgument(3, $config['web']);
        $definition->setArgument(4, $config['admin_ips']);
    }

    final public function prepend(ContainerBuilder $container): void
    {
        $this->prependTwig($container);
        $this->prependFramework($container);
        $this->prependNelmioCors($container);
    }

    private function prependTwig(ContainerBuilder $container): void
    {
        $twigConfig = [
            'paths'       => [
                'assets/assets/images'                  => 'images',
                '%kernel.project_dir%/vendor/xx/yy/zzz' => 'OriginalVNamespace',
            ],
            'globals'     => [
                'oswis' => '@oswis_org_oswis_core.oswis_core_settings_provider',
            ],
            'form_themes' => [
                'bootstrap_4_layout.html.twig',
            ],
            'date'        => [
                'format' => 'j. n. Y H:i',
            ],
        ];
        $container->prependExtensionConfig('twig', $twigConfig);
    }

    private function prependFramework(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig(
            'framework',
            [
                'router'     => ['utf8' => true],
                'php_errors' => ['log' => true],
                'esi'        => ['enabled' => true],
                'fragments'  => [
                    'path'                      => '/_fragment',
                    'hinclude_default_template' => '@OswisOrgOswisCore/web/parts/hinclude.html.twig',
                ],
                'validation' => ['email_validation_mode' => 'html5'],
                'serializer' => [
                    'mapping' => [
                        'paths' => ['%kernel.project_dir%/config/serialization'],
                    ],
                ],
            ]
        );
    }

    private function prependNelmioCors(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig(
            'nelmio_cors',
            [
                'defaults' => [
                    'origin_regex'   => true,
                    'allow_origin'   => ['%env(CORS_ALLOW_ORIGIN)%'],
                    'allow_methods'  => ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE'],
                    'allow_headers'  => ['Content-Type', 'Authorization'],
                    'expose_headers' => ['Link'],
                    'max_age'        => 3600,
                ],
                'paths'    => [
                    '^/' => null,
                ],
            ]
        );
    }
}
