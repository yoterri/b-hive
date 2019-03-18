<?php

namespace User\Db;

use Bhive\Db\AbstractDb;
use Bhive\LazyLoadInterface;

class User extends AbstractDb implements LazyLoadInterface
{
    
    /**
     * @var string
     */
    protected $tableName = 'user';

    /**
     * @var string
     */
    protected $entityClassName = 'User\Entity\User';

}
