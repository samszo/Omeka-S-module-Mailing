<?php
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
    'router' => [
        'routes' => [
            'admin' => [
                'child_routes' => [
                    'mailing' => [
                        'type' => 'Literal',
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
