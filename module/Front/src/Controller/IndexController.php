<?php

namespace Front\Controller;

use Zend;

class IndexController extends Zend\Mvc\Controller\AbstractActionController
{

    function indexAction()
    {
        #$container = $this->getServiceLocator();
        #$adapter = $container->get('adapter');

        #$adapter->query("select version()");
    }
}