<?php declare(strict_types=1);

namespace Mailing;

return [
    'view_manager' => [
        'template_path_stack' => [
            dirname(__DIR__) . '/view',
        ],
    ],
    'controllers' => [
        'invokables' => [
            'Mailing\Controller\Admin\Index' => 'Mailing\Controller\Admin\IndexController',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Mailing\ListmonkService' => Service\ListmonkServiceFactory::class,
        ],
    ],

    'navigation' => [
        'AdminModule' => [
            [
                'label' => 'Mailing',
                'route' => 'admin/mailing',
                'resource' => 'Mailing\Controller\Admin\Index',
                'privilege' => 'browse',
                'class' => 'o-icon-mailing',
            ],
        ],
    ],
    'form_elements' => [
        'invokables' => [
            Form\ConfigForm::class => Form\ConfigForm::class,
            Form\BatchEditFieldset::class => Form\BatchEditFieldset::class,
        ],
    ],
    'mailing' => [
        'config' => [
            'mailing_listmonk_url' => 180,
            'mailing_listmonk_username' => 180,
            'mailing_listmonk_token' => 0,
            'mailing_properties_mail' => ["foaf:mbox"],
            'mailing_properties_data' => ["dcterms:title"],
        ],
    ],

    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'mailing' => [
                        'type' => \Laminas\Router\Http\Literal::class,
                        'options' => [
                            'route' => '/mailing',
                            'defaults' => [
                                '__NAMESPACE__' => 'Mailing\Controller\Admin',
                                'controller' => 'Index',
                                'action' => 'index',
                            ],
                        ],
                        'may_terminate' => true,
                        'child_routes' => [
                            'subscribers' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/subscribers',
                                    'defaults' => [
                                        'action' => 'subscribers',
                                    ],
                                ],
                            ],
                            'lists' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/lists',
                                    'defaults' => [
                                        'action' => 'lists',
                                    ],
                                ],
                            ],
                            'campaigns' => [
                                'type' => 'Literal',
                                'options' => [
                                    'route' => '/campaigns',
                                    'defaults' => [
                                        'action' => 'campaigns',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Mailing\ListmonkService' => 'Mailing\Service\ListmonkServiceFactory',
        ],
    ],
];
