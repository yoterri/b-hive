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

use Bhive\Communicator;
use Bhive\ContainerAwareInterface;
use Bhive\LazyLoadInterface;
use Bhive\InputFilter\AbstractInputFilter;

abstract class AbstractControl implements ContainerAwareInterface, AdapterAwareInterface, EventManagerAwareInterface, LazyLoadInterface
{

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
        return new \Bhive\Communicator();
    }


    /**
     * @param AbstractInputFilter $filter
     * @param Communicator $com
     * @return AbstractControl
     */
    function setFilterError(AbstractInputFilter $filter, Communicator $com = null)
    {
        $messages = $filter->getMessages();

        if(!$com)
        {
            $com = $this->getCommunicator();
        }
        
        foreach($messages as $key => $item)
        {
            $message = current($item);
            $com->addError($message, $key);
        }

        return $com;
    }


    /**
     * @param array | object $data
     * @return Parameters
     */
    function toParams($data)
    {
        if(is_object($data))
        {
            if(method_exists($data, 'toArray'))
            {
                $data = $data->toArray();
            }
            elseif(method_exists($data, 'getArrayCopy'))
            {
                $data = $data->getArrayCopy();
            }
        }

        #
        if(is_array($data))
        {
            $params = new Parameters($data);
        }
        elseif($data instanceof Parameters)
        {
            $params = $data;
        }
        else
        {
            throw new \Exception('Invalid parameter provided');
        }
        
        return $params;
    }


    /**
     * @param Parameters $params
     * @param string $inputFilterKey
     * @param string $dbKey
     *
     * @return Bhive\Communicator
     */
    protected function _save(Parameters $params, $inputFilterKey, $dbKey)
    {
        $sm = $this->getContainer();
        $com = new Communicator();

        try
        {
            $inputFilter = $sm->get($inputFilterKey);
            $inputFilter->build();

            $inputFilter->setData($params->toArray());

            if($inputFilter->isValid())
            {
                $db = $sm->get($dbKey);
                if($params->id)
                {
                    $id = $params->id;

                    #
                    $entity = $db->findByPrimaryKey($id);
                    if($entity)
                    {
                        $values = $inputFilter->getValues(true);
                        $entity->populate($values);

                        #
                        $eventParams = array(
                            'entity' => $entity,
                            'params' => $params,
                            'values' => $values,
                        );
                        $event = $this->_triggerSavingEvent($eventParams);
                        $entity = $event->getParam('entity');

                        #
                        $in = $entity->toArray();

                        $db->doUpdate($in, array('id' => $id));

                        $com->setSuccess('Successfully updated.', array('entity' => $entity));
                    }
                    else
                    {
                        $com->addError('Record not found.');
                    }
                }
                else
                {
                    $entity = $db->getEntity();
                    $values = $inputFilter->getValues();

                    $entity->exchange($values);

                    #
                    $eventParams = array(
                        'entity' => $entity,
                        'params' => $params,
                        'values' => $values,
                    );
                    $event = $this->_triggerSavingEvent($eventParams);
                    $entity = $event->getParam('entity');

                    #
                    $in = $entity->toArray();

                    $id = $db->doInsert($in);

                    $entity->id = $id;

                    $com->setSuccess('Successfully added.', array('entity' => $entity));
                }
            }
            else
            {
                $this->setFilterError($inputFilter, $com);
            }
        }
        catch(\Exception $ex)
        {
            $com->setException($ex);
        }

        return $com;
    }


    private function _triggerSavingEvent($eventParams)
    {
        $event = new Event('control.save.pre', $this, $eventParams);
        $this->getEventManager()->triggerEvent($event);
        return $event;
    }
}