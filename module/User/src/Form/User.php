<?php

namespace User\Form;

use Bhive\Form\AbstractForm;

class User extends AbstractForm
{

  
    function getFields()
    {
        $fields = array(

            array(
                'name' => 'id',
                'type' => 'hidden',
            ),

            array(
                'name' => 'first_name',
                'type' => 'text',
                'options' => array(
                    'label' => 'First name',
                )
            ),

            array(
                'name' => 'last_name',
                'type' => 'text',
                'options' => array(
                    'label' => 'Last name',
                )
            ),
            
            array(
                'name' => 'email',
                'type' => 'email',
                'options' => array(
                    'label' => 'Email',
                )
            ),
            
            array(
                'name' => 'password',
                'type' => 'password',
                'options' => array(
                    'label' => 'Password',
                ),
            ),

            array(
                'name' => 'password_repeat',
                'type' => 'password',
                'options' => array(
                    'label' => 'Repeat password',
                ),
            ),
            
            array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'value' => 'Submit',
                    'id'    => 'submit',
                ),
            ),
        );
        
        return $fields;
    }
}