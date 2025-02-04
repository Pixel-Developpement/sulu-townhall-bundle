<?php

namespace Pixel\TownHallBundle\DependencyInjection;

use Pixel\TownHallBundle\Admin\BulletinAdmin;
use Pixel\TownHallBundle\Admin\DecreeAdmin;
use Pixel\TownHallBundle\Admin\ProcedureAdmin;
use Pixel\TownHallBundle\Admin\ReportAdmin;
use Pixel\TownHallBundle\Entity\Bulletin;
use Pixel\TownHallBundle\Entity\Decree;
use Pixel\TownHallBundle\Entity\Procedure;
use Pixel\TownHallBundle\Entity\Report;
use Sulu\Bundle\PersistenceBundle\DependencyInjection\PersistenceExtensionTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;

class TownHallExtension extends Extension implements PrependExtensionInterface
{
    use PersistenceExtensionTrait;

    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('sulu_admin')) {
            $container->prependExtensionConfig(
                'sulu_admin',
                [
                    'forms' => [
                        'directories' => [
                            __DIR__ . '/../Resources/config/forms',
                        ],
                    ],
                    'lists' => [
                        'directories' => [
                            __DIR__ . '/../Resources/config/lists',
                        ],
                    ],
                    'resources' => [
                        'townhall_settings' => [
                            'routes' => [
                                'detail' => 'townhall.get_townhall-settings',
                            ],
                        ],
                        'reports' => [
                            'routes' => [
                                'detail' => 'townhall.get_report',
                                'list' => 'townhall.get_reports',
                            ],
                        ],
                        'bulletins' => [
                            'routes' => [
                                'detail' => 'townhall.get_bulletin',
                                'list' => 'townhall.get_bulletins',
                            ],
                        ],
                        'procedures' => [
                            'routes' => [
                                'detail' => 'townhall.get_procedure',
                                'list' => 'townhall.get_procedures',
                            ],
                        ],
                        'decrees' => [
                            'routes' => [
                                'detail' => 'townhall.get_decree',
                                'list' => 'townhall.get_decrees',
                            ],
                        ],
                    ],
                    'field_type_options' => [
                        'selection' => [
                            'report_selection' => [
                                'default_type' => 'list_overlay',
                                'resource_key' => Report::RESOURCE_KEY,
                                'view' => [
                                    'name' => ReportAdmin::EDIT_FORM_VIEW,
                                    'result_to_view' => [
                                        'id' => 'id',
                                    ],
                                ],
                                'types' => [
                                    'list_overlay' => [
                                        'adapter' => 'table',
                                        'list_key' => Report::LIST_KEY,
                                        'display_properties' => ['title'],
                                        'icon' => 'fa-map',
                                        'label' => 'townhall.reports',
                                        'overlay_title' => 'townhall_reports.list',
                                    ],
                                ],
                            ],
                            'bulletin_selection' => [
                                'default_type' => 'list_overlay',
                                'resource_key' => Bulletin::RESOURCE_KEY,
                                'view' => [
                                    'name' => BulletinAdmin::EDIT_FORM_VIEW,
                                    'result_to_view' => [
                                        'id' => 'id',
                                    ],
                                ],
                                'types' => [
                                    'list_overlay' => [
                                        'adapter' => 'table',
                                        'list_key' => Bulletin::LIST_KEY,
                                        'display_properties' => ['title'],
                                        'icon' => 'fa-map',
                                        'label' => 'townhall.bulletins',
                                        'overlay_title' => 'townhall_bulletins.list',
                                    ],
                                ],
                            ],
                            'procedure_selection' => [
                                'default_type' => 'list_overlay',
                                'resource_key' => Procedure::RESOURCE_KEY,
                                'view' => [
                                    'name' => ProcedureAdmin::EDIT_FORM_VIEW,
                                    'result_to_view' => [
                                        'id' => 'id',
                                    ],
                                ],
                                'types' => [
                                    'list_overlay' => [
                                        'adapter' => 'table',
                                        'list_key' => Bulletin::LIST_KEY,
                                        'display_properties' => ['title'],
                                        'icon' => 'fa-map',
                                        'label' => 'townhall.procedures',
                                        'overlay_title' => 'townhall_procedure.list',
                                    ],
                                ],
                            ],
                            'decree_selection' => [
                                'default_type' => 'list_overlay',
                                'resource_key' => Decree::RESOURCE_KEY,
                                'view' => [
                                    'name' => DecreeAdmin::EDIT_FORM_VIEW,
                                    'result_to_view' => [
                                        'id' => 'id',
                                    ],
                                ],
                                'types' => [
                                    'list_overlay' => [
                                        'adapter' => 'table',
                                        'list_key' => Decree::LIST_KEY,
                                        'display_properties' => ['title'],
                                        'icon' => 'fa-university',
                                        'label' => 'townhall.decrees',
                                        'overlay_title' => 'townhall.decree.list',
                                    ],
                                ],
                            ],
                        ],
                        'single_selection' => [
                            'single_report_selection' => [
                                'default_type' => 'list_overlay',
                                'resource_key' => Report::RESOURCE_KEY,
                                'view' => [
                                    'name' => ReportAdmin::EDIT_FORM_VIEW,
                                    'result_to_view' => [
                                        'id' => 'id',
                                    ],
                                ],
                                'types' => [
                                    'list_overlay' => [
                                        'adapter' => 'table',
                                        'list_key' => Report::LIST_KEY,
                                        'display_properties' => ['title'],
                                        'icon' => 'fa-map',
                                        'empty_text' => 'townhall_reports.empty',
                                        'overlay_title' => 'townhall_reports.list',
                                    ],
                                    'auto_complete' => [
                                        'display_property' => 'title',
                                        'search_properties' => ['title'],
                                    ],
                                ],
                            ],
                            'single_bulletin_selection' => [
                                'default_type' => 'list_overlay',
                                'resource_key' => Bulletin::RESOURCE_KEY,
                                'view' => [
                                    'name' => BulletinAdmin::EDIT_FORM_VIEW,
                                    'result_to_view' => [
                                        'id' => 'id',
                                    ],
                                ],
                                'types' => [
                                    'list_overlay' => [
                                        'adapter' => 'table',
                                        'list_key' => Bulletin::LIST_KEY,
                                        'display_properties' => ['title'],
                                        'icon' => 'fa-map',
                                        'empty_text' => 'townhall_bulletins.empty',
                                        'overlay_title' => 'townhall_bulletins.list',
                                    ],
                                    'auto_complete' => [
                                        'display_property' => 'title',
                                        'search_properties' => ['title'],
                                    ],
                                ],
                            ],
                            'single_procedure_selection' => [
                                'default_type' => 'list_overlay',
                                'resource_key' => Procedure::RESOURCE_KEY,
                                'view' => [
                                    'name' => ProcedureAdmin::EDIT_FORM_VIEW,
                                    'result_to_view' => [
                                        'id' => 'id',
                                    ],
                                ],
                                'types' => [
                                    'list_overlay' => [
                                        'adapter' => 'table',
                                        'list_key' => Procedure::LIST_KEY,
                                        'display_properties' => ['title'],
                                        'icon' => 'fa-map',
                                        'empty_text' => 'townhall_procedure.empty',
                                        'overlay_title' => 'townhall_procedure.list',
                                    ],
                                    'auto_complete' => [
                                        'display_property' => 'title',
                                        'search_properties' => ['title'],
                                    ],
                                ],
                            ],
                            'single_decree_selection' => [
                                'default_type' => 'list_overlay',
                                'resource_key' => Decree::RESOURCE_KEY,
                                'view' => [
                                    'name' => DecreeAdmin::EDIT_FORM_VIEW,
                                    'result_to_view' => [
                                        'id' => 'id',
                                    ],
                                ],
                                'types' => [
                                    'list_overlay' => [
                                        'adapter' => 'table',
                                        'list_key' => Decree::LIST_KEY,
                                        'display_properties' => ['title'],
                                        'icon' => 'fa-university',
                                        'empty_text' => 'townhall.decree.emptyText',
                                        'overlay_title' => 'townhall.decree.list',
                                    ],
                                    'auto_complete' => [
                                        'display_property' => 'title',
                                        'search_properties' => ['title'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            );
        }
        if ($container->hasExtension('sulu_search')) {
            $container->prependExtensionConfig(
                'sulu_search',
                [
                    'indexes' => [
                        'procedure' => [
                            'name' => 'townhall.procedures',
                            'icon' => 'su-house',
                            'view' => [
                                'name' => ProcedureAdmin::EDIT_FORM_VIEW,
                                'result_to_view' => [
                                    'id' => 'id',
                                    'locale' => 'locale',
                                ],
                            ],
                            'security_context' => Procedure::SECURITY_CONTEXT,
                        ],
                    ],
                    'website' => [
                        "indexes" => [
                            "procedure",
                        ],
                    ],
                ]
            );
        }
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loaderYaml = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loaderYaml->load('services.yaml');
        //$this->configurePersistence($config['objects'], $container);
    }
}
