<?php

namespace Bhive\Factory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class Controller implements AbstractFactoryInterface
{

    public function canCreateServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName)
    {
        $flag = class_exists($requestedName . 'Controller');
        return $flag;
    }


    public function createServiceWithName(ServiceLocatorInterface $locator, $name, $requestedName)
    {
        $class = $requestedName . 'Controller';
        return new $class();
    }
}