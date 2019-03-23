<?php

namespace Bhive\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Interop\Container\ContainerInterface;
use Bhive\Communicator;
use Bhive\Form\AbstractForm;

abstract class AbstractController extends AbstractActionController
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     *
     * @var message
     */
    protected $viewVars = array();


    /**
     *
     * @param mixed $key
     * @param string $Value
     * @return \Bhive\Controller\AbstractController
     */
    function assign($key, $value = null)
    {
        if(is_object($key))
        {
            if(method_exists($key, 'toArray'))
            {
                $key = $key->toArray();
            }
            elseif(method_exists($key, 'getArrayCopy'))
            {
                $key = $key->getArrayCopy();
            }
        }
        
        if(is_array($key)) 
        {
            foreach($key as $a => $b)
            {
                $this->viewVars[$a] = $b;
            }
        }
        else
        {
            $this->viewVars[$key] = $value;
        }
        
        return $this;
    }


    /**
     * @param ContainerInterface $container
     */
    function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }


    function getContainer()
    {
        return $this->container;
    }

    /**
     * @param string $key
     */
    function basicAuthentication($key = 'default')
    {
        $sl = $this->getServiceLocator();
        
        $basic = new \Bhive\Auth\Http\Basic($sl);
        
        $basic->authenticate($key);
    }

    /**
     * Translate a string using the given text domain and locale
     *
     * @param string $str
     * @param array $params
     * @param string $textDomain
     * @param string $locale
     * @return string
     */
    function _($str, $params = array(), $textDomain = 'default', $locale = null)
    {
        $sl = $this->getServiceLocator();
        $str = $sl->get('translator')->translate($str, $textDomain, $locale);
        
        if(is_array($params) && count($params))
        {
            array_unshift($params, $str);
            $str = call_user_func_array('sprintf', $params);
        }
        
        return $str;
    }
    
    /**
     * Action called if matched action does not exist
     *
     * @return array
     */
    public function unauthorizedAction()
    {
        $event = $this->getEvent();
        $event->getResponse()->setStatusCode(401);
        
        $viewModel = new \Zend\View\Model\ViewModel();
        $viewModel->setTemplate('error/401.phtml');
                
        return $viewModel;
    }

    
    /**
     * @param AbstractForm $form
     * @param AbstractControl|array $messages
     */
    function setFormMessages(AbstractForm &$form, $messages)
    {
        $errors = array();
        if($messages instanceof Communicator)
        {
            $errors = $messages->getErrors();
        }
        elseif(is_array($messages))
        {
            $errors = $messages;
        }

        if($errors)
        {
            $form->setMessages($errors);
        }

        return $this;
    }
}