<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Film\Controller\Film' => 'Film\Controller\FilmController',
            'Film\Controller\Auth' => 'Film\Controller\AuthController',
            'Film\Controller\Success' => 'Film\Controller\SuccessController'
        ),
    ),

    // The following section is new and should be added to your file
    'router' => array(
        'routes' => array(
            'Film' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/film[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Film\Controller\Film',
                        'action' => 'index',
                    ),
                ),
            ),
            'login' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/auth',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Film\Controller',
                        'controller' => 'Auth',
                        'action' => 'login',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Film\Controller\Auth',
                                'action' => 'login',
                            ),
                        ),
                    ),
                ),
            ),
            'success' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/success',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Film\Controller',
                        'controller' => 'Success',
                        'action' => 'index',
                    ),
                ),
            ),
        ),
    ),

    'view_manager' => array(
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            'Film' => __DIR__ . '/../view',
        ),
    ),
);