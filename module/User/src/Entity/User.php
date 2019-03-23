<?php

namespace User\Entity;

use Bhive\Entity\AbstractEntity;
use Bhive\LazyLoadInterface;

class User extends AbstractEntity implements LazyLoadInterface
{
    /**
     * @var string
     */
    protected $dbClassName = 'User\Db\User';

    /**
     * @var array 
     */
    protected $primaryKeyColumn = array('id');
    
    /**
     * @var array 
     */
    protected $properties = array(
        'id'
        ,'first_name'
        ,'last_name'
        ,'email'
        ,'password'
        ,'status'
        ,'created_on'
    );



    function isPasswordEncrypted()
    {
        return (60 == (strlen($this->password)) && preg_match('/^\$2y\$/', $this->password));
    }
}

