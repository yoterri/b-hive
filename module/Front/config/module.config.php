<?php
return array(

    'event_listeners' => array(
        array(
            'source_class' => 'User\InputFilter\User',
            'event_name' => 'pre.validate',

            'callback' => array(
                'User\Control', 'onPreValidateUser'
            ),
        ),

        array(
            'source_class' => 'User\Control',
            'event_name' => 'pre.insert.user.db.user',
            'callback' => array(
                'User\Control', 'onPreInsertUser'
            ),
            'priority' => 1,
        ),

        array(
            'source_class' => 'User\Control',
            'event_name' => 'pre.update.user.db.user',
            'callback' => array(
                'User\Control', 'onPreUpdateUser'
            ),
        ),

        /*
        array(
            'source_class' => 'User\Form\User',
            'event_name' => 'pre.build',
            'callback' => array(
                'User\Control', 
            ),
            'priority' => 1,
        ),
        */
    ),

    'router' => array(

        'routes' => array(

            'default' => array(
                'type' => 'Zend\Router\Http\Segment',
                'options' => array(
                    'route' => '/:module/:controller/:action',
                    'constraints' => array(),
                    'defaults' => array(
                        
                    ),
                ),
                
                'may_terminate' => true,
                
                'child_routes' => array(
                    'wildcard' => array(
                        'type' => 'Wildcard',
                    ),
                ),
            ),

            'front' => array(
                'type' => 'Zend\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'constraints' => array(),
                    'defaults' => array(
                        '__NAMESPACE__' => 'Front\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                
                'may_terminate' => true,
                
                'child_routes' => array(
                    'wildcard' => array(
                        'type' => 'Wildcard',
                    ),
                ),
            ),
        ),
    ),
    
    'view_manager' => array(
        
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),    
);
