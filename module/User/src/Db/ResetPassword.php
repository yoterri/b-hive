<?php

namespace User\Db;

use Bhive\Db\AbstractDb;
use Bhive\LazyLoadInterface;

class ResetPassword extends AbstractDb implements LazyLoadInterface
{
    
    /**
     * @var string
     */
    protected $tableName = 'user_reset_password';

    /**
     * @var string
     */
    protected $entityClassName = 'User\Entity\ResetPassword';

}
