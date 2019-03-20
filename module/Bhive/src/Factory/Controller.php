<?php

namespace Bhive\Factory;

use Zend\ServiceManager\Factory\AbstractFactoryInterface;
use Interop\Container\ContainerInterface;
use Bhive\Controller\AbstractController;

class Controller implements AbstractFactoryInterface
{

    public function canCreate(ContainerInterface $container, $requestedName)
    {
        #$request = $container->get('request');
        #$router = $container->get('router');
        #$route = $router->match($request);

        return class_exists($requestedName . 'Controller');
    }


    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $controllerClass = $requestedName . 'Controller';
        $controller = new $controllerClass();

        if($controller instanceof AbstractController)
        {
            $controller->setContainer($container);
        }

        return $controller;
    }
}