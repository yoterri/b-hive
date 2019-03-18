<?php

namespace Bhive\Control;
use Interop\Container\ContainerInterface;
use Zend\InputFilter\InputFilter;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Sql\Where;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\Db\Adapter\Adapter;
use Zend\EventManager\EventManager;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\Parameters;

use Bhive\ContainerAwareInterface;
use Bhive\LazyLoadInterface;
use Bhive\InputFilter\AbstractInputFilter;

abstract class AbstractControl implements ContainerAwareInterface, AdapterAwareInterface, EventManagerAwareInterface, LazyLoadInterface
{

    /**
     *
     * @var Bhive\Communicator
     */
    protected $communicator;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @var Zend\Db\Adapter\Adapter
     */
    protected $adapter;


    /**
     * @param ContainerInterface $container
     */
    function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    
    /**
     * @return ContainerInterface
     */
    function getContainer()
    {
        return $this->container;
    }


    /**
     * Translate a message using the given text domain and locale
     *
     * @param string $message
     * @param string $textDomain
     * @param string $locale
     * @return string
     */
    function _($message, $textDomain = 'default', $locale = null)
    {
        $sm = $this->getContainer();

        if($sm->has('translator'))
        {
            $message = $sm->get('translator')->translate($message, $textDomain, $locale);
        }

        return $message;
    }


    /**
     * @param $eventManager EventManagerInterface
     */
    function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->addIdentifiers(array(
            get_called_class()
        ));
    
        $this->eventManager = $eventManager;
        
        # $this->getEventManager()->trigger('sendTweet', null, array('content' => $content));
        return $this;
    }
    
    
    /**
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if(null === $this->eventManager)
        {
            $this->setEventManager(new EventManager());
        }

        return $this->eventManager;
    }


    /**
     * @param string $eventName
     * @param array $eventParams
     * @return Event
     */
    function triggerEvent($eventName, array $eventParams)
    {
        $event = new Event($eventName, $this, $eventParams);
        $this->getEventManager()->triggerEvent($event);
        return $event;
    }


    /**
     *
     * @param Adapter $adapter
     */
    function setDbAdapter(Adapter $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }


    /**
     *
     * @return \Zend\Db\Adapter\Adapter
     */
    function getDbAdapter()
    {
        return $this->adapter;
    }


    /**
     * @return \Bhive\Communicator
     */
    function getCommunicator()
    {
        if(!$this->communicator instanceof \Bhive\Communicator)
        {
            $this->resetCommunicator();
        }
        
        return $this->communicator;
    }


    /**
     *
     * @return \Bhive\Control\AbstractControl
     */
    function resetCommunicator()
    {
        $this->communicator = new \Bhive\Communicator();
        
        return $this;
    }


    /**
     * @param AbstractInputFilter $filter
     * @return AbstractControl
     */
    function setFilterError(AbstractInputFilter $filter)
    {
        $messages = $filter->getMessages();
        $com = $this->getCommunicator();

        foreach($messages as $key => $item)
        {
            $message = current($item);
            $com->addError($message, $key);
        }

        return $this;
    }
}