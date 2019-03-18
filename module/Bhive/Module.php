<?php

namespace Bhive;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\Event;

class Module
{

    public function onBootstrap(Event $e)
    {
        $this->_phpSettings($e);
    }
    
    
    
    protected function _phpSettings($e)
    {
        $app = $e->getParam ('application');
        $config = $app ->getConfig();
        $phpSettings = isset($config['php_settings']) ? $config['php_settings'] : false;

        if(is_array($phpSettings))
        {
            if(isset($phpSettings['error_reporting']))
            {
                error_reporting($phpSettings['error_reporting']);
                unset($phpSettings['error_reporting']);
            }

            foreach($phpSettings as $key => $value)
            {
                ini_set($key, $value);
            }
        }
    }

    
    public function getConfig()
    {
        return array();
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
