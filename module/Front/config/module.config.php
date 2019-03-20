<?php
return array(
    
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
