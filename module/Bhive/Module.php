<?php

namespace Bhive;

use Zend;
#use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
#use Zend\EventManager\Event;

class Module
{

    public function onBootstrap(MvcEvent $event)
    {
        $this->_phpSettings($event);
        $this->_setupRouting($event);
        $this->_setupTablePrefix($event);
    }


    protected function _setupTablePrefix(MvcEvent $event)
    {
        $application = $event->getApplication();

        $eventManager = $application->getEventManager();
        $serviceManager = $application->getServiceManager();
        $config = $serviceManager->get('config');

        if(isset($config['db_prefix']) && ('' != $config['db_prefix']))
        {
            $eventManager->getSharedManager()->attach('Bhive\Db\AbstractDb', 'prefixing', function (\Zend\EventManager\Event $event) use($config)
            {
                if(isset($config['db_prefix']))
                {
                    $event->setParam('prefix', $config['db_prefix']);
                }
            });
        }
    }
    
    
    protected function _setupRouting(MvcEvent $event)
    {
        $eventManager = $event->getApplication()->getEventManager();

        $eventManager->attach(Zend\Mvc\MvcEvent::EVENT_ROUTE, function (Zend\Mvc\MvcEvent $e)
        {
            $route = $e->getRouteMatch();
            
            $module = $route->getParam('module');

            #
            $controller = $route->getParam('controller');
            if(strpos($controller, '-'))
            {
                $exploded = explode(' ', ucwords(str_replace('-', ' ', $controller)));
                $controller = implode('\\', $exploded);
            }

            #
            if(strpos($controller, '_'))
            {
                $exploded = explode(' ', ucwords(str_replace('_', ' ', $controller)));
                $controller = implode('_', $exploded);
            }

            #
            $namespace = $route->getParam('__NAMESPACE__');
            if(!empty($namespace))
            {
                $newController = "$namespace\\$controller";
            }
            else
            {
                $controller = ucfirst($controller);
                $newController = "Controller\\$controller";
            }


            if(!empty($module))
            {
                $module = ucfirst($module);
                $newController = "$module\\$newController";
            }

            #
            $route->setParam('controller', $newController);
        });
    }
    
    protected function _phpSettings(MvcEvent $event)
    {
        $app = $event->getParam ('application');
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
