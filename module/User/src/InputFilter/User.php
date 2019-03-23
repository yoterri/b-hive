<?php
namespace User\InputFilter;

use Bhive;
use Bhive\InputFilter\AbstractInputFilter;
use Zend\InputFilter\Input;
use Zend\Filter;
use Zend\Validator;
use Zend\Db\Sql\Where;

class User extends AbstractInputFilter
{

     function getFilters()
     {
        $sm = $this->getContainer();

        $dbUser = $sm->get('User\Db\User');

        $filters = array(
            [
                'name' => 'id',
                'required' => false,
                'filters' => [
                    [
                        'name' => Filter\ToInt::class
                    ],
                ],

                'validators' => [
                    [
                        'name' => Bhive\Validator\Db\RecordExists::class,
                        'options' => [

                            'adapter' => $dbUser->getAdapter(),
                            'table'  => $dbUser->getTable(),
                            'field'  => 'id',
                            'byPassValue' => 0,

                            'messages' => [
                                Validator\Db\RecordExists::ERROR_NO_RECORD_FOUND => $this->_('The specified User was not found in the database') 
                            ],
                        ],
                    ],
                ],
            ],


            [
                'name' => 'first_name',
                'required' => true,
                'filters' => [
                    [
                        'name' => Filter\StripTags::class
                    ],
                    [
                        'name' => Filter\StringTrim::class, 
                        'options'=>[
                            'charlist'=>"\r\n\t "
                        ],
                    ],
                ],

                'validators' => [
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 150,
                        ],
                    ],
                ],
            ],


            [
                'name' => 'last_name',
                'required' => true,
                'filters' => [
                    [
                        'name' => Filter\StripTags::class
                    ],
                    [
                        'name' => Filter\StringTrim::class, 
                        'options'=>[
                            'charlist'=>"\r\n\t "
                        ],
                    ],
                ],

                'validators' => [
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 150,
                        ],
                    ],
                ],
            ],


            [
                'name' => 'email',
                'required' => true,
                'filters' => [
                ],
                'validators' => [
                    [
                        'name' => Validator\EmailAddress::class,
                    ],
                    [
                        'name' => Bhive\Validator\Db\NoRecordExists::class,
                        'options' => [
                            'table' => $dbUser->getTable(),
                            'field' => 'email',
                            'adapter' => $dbUser->getAdapter(),
                            'exclude' => [
                                'field' => 'id',
                                'form_value' => 'id',
                            ],
                            'messages' => [
                                Validator\Db\NoRecordExists::ERROR_RECORD_FOUND => $this->_('Alreay exist a registered User with the provided email address') 
                            ],
                        ],
                    ],
                ],
            ],

            
            [
                'name' => 'password',
                'required' => true,
                'filters' => [
                ],
                'validators' => [
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min' => 6,
                            'max' => 300,
                        ],
                    ],
                ],
            ],


            [
                'name' => 'password_repeat',
                'required' => true,
                'filters' => [
                ],
                'validators' => [
                    [
                        'name' => Validator\Identical::class,
                        'options' => [
                            'token' => 'password',

                            'messages' => [
                                Validator\Identical::NOT_SAME => $this->_('Password does not match') 
                            ],
                        ],
                    ],
                ],
            ],

        );

        return $filters;
     }
}