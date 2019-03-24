<?php

namespace User;

use Bhive\Control\AbstractControl;
use Bhive\Communicator;

use Zend\Stdlib\Parameters;
use Zend\Crypt\Password\Bcrypt;

class Control extends AbstractControl
{

    /**
     * @param array $data
     * @return Bhive\Communicator
     */
    function saveUser($data)
    {
        $sm = $this->getContainer();

        $params = $this->toParams($data);

        $filterKey = 'User\InputFilter\User';
        $dbKey = 'User\Db\User';

        #
        return $this->_save($params, $filterKey, $dbKey);
    }






    


    /**
     * @event_handler
     * @class User\InputFilter\User
     * @event pre.validate
     */
    function onPreValidateUser($event)
    {
        $filter = $event->getTarget();

        #
        $values = $filter->getValues();
        $params = new Parameters($values);

        # if editing we have to check if the user is not providing a password,
        # in such case we have to remove passwords field from the filters
        if($params->id)
        {
            if(!$params->password)
            {
                $remove = array('password', 'password_repeat');
                $filter->removeFields($remove);
            }
        }
    }


    /**
     * @event_handler
     * @class User\Control
     * @event pre.insert.user.db.user
     */
    function onPreInsertUser($event)
    {
        exit("1");
        $entity = $event->getParam('entity');

        if($entity->password)
        {
            $crypt = new Bcrypt();
            $entity->password = $crypt->create($entity->password);
        }
        
        if(!$entity->created_on)
        {
            $entity->created_on = date('Y-m-d H:i:s');
        }
        
        if(is_null($entity->status) || ('' === $entity->status))
        {
            $entity->status = 2;
        }
        
        #
        $event->setParam('entity', $entity);
    }


    /**
     * @event_handler
     * @class User\Control
     * @event pre.update.user.db.user
     */
    function onPreUpdateUser($event)
    {
        $entity = $event->getParam('entity');
        
        if(!$entity->isPasswordEncrypted())
        {
            if($entity->password)
            {
                $crypt = new Bcrypt();
                $entity->password = $crypt->create($entity->password);
            }
        }

        $event->setParam('entity', $entity);
    }
}