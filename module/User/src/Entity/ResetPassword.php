<?php

namespace User\Entity;

use Bhive\Entity\AbstractEntity;
use Bhive\LazyLoadInterface;

class ResetPassword extends AbstractEntity implements LazyLoadInterface
{
    /**
     * @var string
     */
    protected $dbClassName = 'User\Db\ResetPassword';

    /**
     * @var array 
     */
    protected $primaryKeyColumn = array('email');
    
    /**
     * @var array 
     */
    protected $properties = array(
        'email'
        ,'key'
        ,'created_on'
    );
}

