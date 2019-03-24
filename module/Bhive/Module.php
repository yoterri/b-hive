<?php

namespace Bhive;

use Zend;
#use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;
#use Zend\EventManager\Event;

class Module
{

    public function onBootstrap(MvcEvent $event)
    {
        $this->_phpSettings($event);
        $this->_setupRouting($event);
        $this->_setupTablePrefix($event);
        $this->_setupEventHandlers($event);        
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


    protected function _setupEventHandlers(MvcEvent $event)
    {
        $sm = $event->getApplication()->getServiceManager();

        $app = $event->getParam ('application');
        $config = $app ->getConfig();

        if(isset($config['event_listeners']) && is_array($config['event_listeners']))
        {
            foreach($config['event_listeners'] as $item)
            {
                if(!isset($item['source_class']) || ('' == $item['source_class']))
                {
                    throw new \Exception("Can't install event listener. Source class wasn't defined");
                }

                if(!isset($item['event_name']) || ('' == $item['event_name']))
                {
                    throw new \Exception("Can't install event listener for event '{$item['source_class']}'. Event name wasn't defined");
                }

                if(!isset($item['callback']) || ('' == $item['callback']))
                {
                    throw new \Exception("Can't install event listener for event '{$item['source_class']}->{$item['event_name']}'. Callback wasn't defined");
                }

                if(!is_callable($item['callback']))
                {
                    throw new \Exception("Can't install event listener for event '{$item['source_class']}->{$item['event_name']}'. Defined callback isn't callable");
                }

                $source = $sm->get($item['source_class']);
                if(!method_exists($source, 'getEventManager'))
                {
                    throw new \Exception("Can't install event listener for event '{$item['source_class']}->{$item['event_name']}'. Class {$item['source_class']} hasn't defined method named 'getEventManager()'");
                }

                #
                $eManager = $source->getEventManager();
                if(!$eManager instanceof EventManagerInterface)
                {
                    throw new \Exception("Can't install event listener for event '{$item['source_class']}->{$item['event_name']}'. '{$item['source_class']}::getEventManager()' method must return a valid EventManagerInterface object");
                }

                $priority = isset($item['priority']) ? (int)$item['priority'] : 1;
                if($priority < 0)
                {
                    $priority = 1;
                }


                #
                if(is_array($item['callback']))
                {
                    $listener = $sm->get($item['callback'][0]);
                    $callback = array($listener, $item['callback'][1]);
                }
                else
                {
                    $callback = $item['callback'];
                }
                
                $source->getEventManager()->attach($item['event_name'], $callback);
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
