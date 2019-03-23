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

        # if editing we have to check if the user is not providing a password,
        # in such case we have to remove passwords field from the filters
        if($params->id)
        {
            $sm->get($filterKey)->getEventManager()->attach('inputfilter.pos.build', function($event) use($params) {
                $filter = $event->getTarget();

                if(!$params->password)
                {
                    $remove = array('password', 'password_repeat');
                    $filter->removeFields($remove);
                }
            });
        }

        #
        $this->getEventManager()->attach('control.save.pre', function($event) {

            $entity = $event->getParam('entity');

            # editing
            if($entity->id)
            {
                if(!$entity->isPasswordEncrypted())
                {
                    $crypt = new Bcrypt();

                    $entity->password = $crypt->create($entity->password);
                }
            }
            else
            {
                $crypt = new Bcrypt();

                $entity->password = $crypt->create($entity->password);
                $entity->created_on = date('Y-m-d H:i:s');
                $entity->status = 2;
            }
            
            #
            $event->setParam('entity', $entity);
        });

        return $this->_save($params, $filterKey, $dbKey);
    }
}