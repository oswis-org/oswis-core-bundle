<?php

namespace OswisOrg\OswisCoreBundle\DependencyInjection;

use Exception;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUser;
use OswisOrg\OswisCoreBundle\Entity\AppUser\AppUserRole;
use OswisOrg\OswisCoreBundle\Interfaces\Extender\UpdateExtenderInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Web\RssExtenderInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Web\SiteMapExtenderInterface;
use OswisOrg\OswisCoreBundle\Interfaces\Web\WebMenuExtenderInterface;
use OswisOrg\OswisCoreBundle\Security\AppUserProvider;
use OswisOrg\OswisCoreBundle\Security\WebUserAuthenticator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\BadMethodCallException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use function dirname;

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
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
//        $loader->load('services_subscribers.yaml');
//        $loader->load('services_services.yaml');
//        $loader->load('services_utils.yaml');
//        $loader->load('services_web.yaml');
//        $loader->load('services_twig.yaml');
        $configuration = $this->getConfiguration($configs ?? [], $container);
        if ($configuration) {
            $config = $this->processConfiguration($configuration, $configs ?? []);
            $this->oswisCoreSettingsProvider($container, $config);
        }
        $container->registerForAutoconfiguration(SiteMapExtenderInterface::class)->addTag('oswis.site_map_extender');
        $container->registerForAutoconfiguration(RssExtenderInterface::class)->addTag('oswis.rss_extender');
        $container->registerForAutoconfiguration(WebMenuExtenderInterface::class)->addTag('oswis.web_menu_extender');
        $container->registerForAutoconfiguration(UpdateExtenderInterface::class)->addTag('oswis.update_extender');
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
        $definition->setArgument(5, $config['angular_admin']);
    }

    final public function prepend(ContainerBuilder $container): void
    {
        try {
            $configs = $container->getExtensionConfig($this->getAlias());
        } catch (BadMethodCallException) {
            $configs = [];
        }
        $config = $this->processConfiguration(new Configuration(), $configs);
        $this->prependTwig($container);
        $this->prependFramework($container);
        $this->prependSecurity($container);
        $this->prependNelmioCors($container);
        $this->prependApiPlatform($container, $config);
        self::prependForBundleTemplatesOverride($container, ['Twig']);
    }

    private function prependTwig(ContainerBuilder $container): void
    {
        $twigConfig = [
            'default_path'         => '%kernel.project_dir%/templates',
            'debug'                => '%kernel.debug%',
            'strict_variables'     => '%kernel.debug%',
            'exception_controller' => null,
            'paths'                => [
                'assets/assets/images'             => 'images',
                'public/bundles/oswisorgoswiscore' => 'oswis',
            ],
            'globals'              => ['oswis' => '@oswis_org_oswis_core.oswis_core_settings_provider'],
            'form_themes'          => ['bootstrap_5_layout.html.twig'],
            'date'                 => ['format' => 'j. n. Y H:i'],
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
                'secret'     => '%env(APP_SECRET)%',
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
                'mailer'     => [
                    'dsn' => '%env(MAILER_DSN)%',
                ],
                'assets'     => ['json_manifest_path' => '%kernel.project_dir%/public/build/manifest.json'],
            ]
        );
    }

    private function prependSecurity(ContainerBuilder $container): void
    {
        $config = [
            'enable_authenticator_manager' => true,
            'encoders'                     => [
                AppUser::class => [
                    'algorithm' => 'auto',
                ],
            ],
            'providers'                    => [
                'app_user_provider' => [
                    'id' => AppUserProvider::class,
                ],
            ],
            'firewalls'                    => [
                'dev'               => [
                    'pattern'  => '^/(_(profiler|wdt)|css|images|js)/',
                    'security' => false,
                ],
                'api_token_refresh' => [
                    'pattern'     => '^/api/token/refresh',
                    'stateless'   => true,
                    'refresh_jwt' => [],
                ],
                'api_login'         => [
                    'pattern'    => '^/api/login',
                    'stateless'  => true,
                    'provider'   => 'app_user_provider',
                    'json_login' => [
                        'check_path'      => '/api/login',
                        'success_handler' => 'lexik_jwt_authentication.handler.authentication_success',
                        'failure_handler' => 'lexik_jwt_authentication.handler.authentication_failure',
                    ],
                ],
                'api'               => [
                    'pattern'   => '^/api',
                    'stateless' => true,
                    'provider'  => 'app_user_provider',
                    'guard'     => [
                        'authenticators' => ['lexik_jwt_authentication.jwt_token_authenticator'],
                    ],
                ],
                'main'              => [
                    'provider'  => 'app_user_provider',
                    'stateless' => false,
                    'lazy'      => true,
                    'guard'     => [
                        'authenticators' => [WebUserAuthenticator::class],
                    ],
                    'logout'    => ['path' => 'oswis_org_oswis_core_web_admin_logout'],
                ],
            ],
            'access_control'               => [
                ['path' => '^/web_admin/login', 'roles' => 'PUBLIC_ACCESS'],
                ['path' => '^/web_admin', 'roles' => AppUserRole::ROLE_CUSTOMER],
                ['path' => '^/api/token/refresh', 'roles' => 'PUBLIC_ACCESS'],
                ['path' => '^/api/login', 'roles' => 'PUBLIC_ACCESS'],
                ['path' => '^/api/register', 'roles' => 'PUBLIC_ACCESS'],
            ],
        ];
        foreach (AppUserRole::ROLES_PARENT as $role => $parent) {
            $config['role_hierarchy'][$role] = $parent;
        }
        $container->prependExtensionConfig('security', $config);
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

    private function prependApiPlatform(ContainerBuilder $container, array $config): void
    {
        $container->prependExtensionConfig(
            'api_platform',
            [
                'title'                   => $config['app']['name'] ?? null,
                'description'             => $config['app']['description'] ?? null,
                'version'                 => $config['app']['version'] ?? null,
                'allow_plain_identifiers' => true,
                'eager_loading'           => [
                    'enabled'       => true,
                    'fetch_partial' => false,
                    'max_joins'     => 40,
                    'force_eager'   => true,
                ],
                'swagger'                 => [
                    'versions' => [3],
                    'api_keys' => [
                        'apiKey' => [
                            'name' => 'Authorization',
                            'type' => 'header',
                        ],
                    ],
                ],
                'collection'              => [
                    'pagination' => [
                        'items_per_page'                => 5000,
                        'client_enabled'                => true,
                        'client_items_per_page'         => true,
                        'items_per_page_parameter_name' => 'itemsPerPage',
                        'enabled_parameter_name'        => 'pagination',
                    ],
                ],
                'formats'                 => [
                    'json'    => ['mime_types' => ['application/json']],
                    'jsonld'  => ['mime_types' => ['application/ld+json']],
                    'jsonapi' => ['mime_types' => ['application/vnd.api+json']],
                    'html'    => ['mime_types' => ['text/html']],
                    'csv'     => ['mime_types' => ['text/csv']],
                    'xml'     => ['mime_types' => ['application/xml']],
                ],
                'error_formats'           => [
                    'jsonproblem' => ['mime_types' => ['application/problem+json']],
                    'jsonapi'     => ['mime_types' => ['application/vnd.api+json']],
                    'jsonld'      => ['mime_types' => ['application/ld+json']],
                ],
                'patch_formats'           => ['json' => ['application/merge-patch+json']],
                'mapping'                 => [
                    'paths' => [
                        '%kernel.project_dir%/vendor/oswis-org/oswis-core-bundle/Entity',
                        '%kernel.project_dir%/vendor/oswis-org/oswis-core-bundle/Api/Dto',
                    ],
                ],
            ]
        );
    }

    /**
     * This work-around allows overriding of other bundles templates by OswisCore.
     *
     * @param  ContainerBuilder  $container
     * @param  array  $bundleNames
     */
    final public static function prependForBundleTemplatesOverride(ContainerBuilder $container, array $bundleNames): void
    {
        $twigConfigs = $container->getExtensionConfig('twig');
        $paths = [];
        foreach ($twigConfigs as $twigConfig) {
            if (isset($twigConfig['paths'])) {
                $paths += $twigConfig['paths'];
            }
        }
        foreach ($bundleNames as $bundleName) {
            $paths['templates/bundles/'.$bundleName.'Bundle/'] = $bundleName;
            $paths[dirname(__DIR__).'/Resources/views/bundles/'.$bundleName.'Bundle/'] = $bundleName;
        }
        $container->prependExtensionConfig(
            'twig',
            [
                'paths'        => $paths,
                'default_path' => '%kernel.project_dir%/templates',
            ]
        );
    }
}
