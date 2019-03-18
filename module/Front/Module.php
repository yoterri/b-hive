<?php

namespace Front;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;

class Module
{

    public function onBootstrap(Event $e)
    {
        ;
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }



    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src',
                ),
            ),
        );
    }

}
