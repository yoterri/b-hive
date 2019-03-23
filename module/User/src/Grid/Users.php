<?php

namespace User\Grid;

use Com\DataGrid\TinyGrid;
use Zend\Db\Sql\Select;
use Bhive\LazyLoadInterface;
use Bhive\Grid\Builder;

class Users extends Builder implements LazyLoadInterface
{
    

    protected function _getSource()
    {
        $sm = $this->getContainer();
        $dbUser = $sm->get('User\Db\User');
        return $dbUser->getSql()->select();
    }


    protected function _getColumns()
    {
        $columns = array(

            'id' => array(
                'header' => array(
                    'attributes' => array('style' => 'width:80px'),
                    'label' => 'ID',
                    'sort' => true,
                    'filter' => 1,
                    'filter_type' => '=',
                ),
            ),

            'first_name' => array(
                'header' => array(
                    'label' => 'First name',
                    'sort' => true,
                    'filter' => 1,
                ),
            ),

            'last_name' => array(
                'header' => array(
                    'label' => 'Last name',
                    'sort' => true,
                    'filter' => 1,
                ),
            ),

            'email' => array(
                'header' => array(
                    'label' => 'Email',
                    'sort' => true,
                    'filter' => 1,
                ),
            ),

            'status' => array(
                'header' => array(
                    'label' => 'Status',
                    'sort' => true,
                    'filter' => 1,
                    'filter_values' => [
                        0 => 'Disabled',
                        1 => 'Enabled',
                        2 => 'Unconfirmed',
                    ],
                ),
                'cell' => array(
                    'type' => 'enum',
                    'source' => array(
                        0 => '<span class="badge badge-secondary">Disabled</span>',
                        1 => '<span class="badge badge-success">Enabled</span>',
                        2 => '<span class="badge badge-warning">Unconfirmed</span>',
                    ),
                ),
            ),

            'created_on' => array(
                'header' => array(
                    'label' => 'Created on',
                    'sort' => true,
                    'filter' => 1,
                ),
                'cell' => array(
                    'type' => 'date',
                    'date_format_to' => 'M j, Y',
                ),
            ),

        );

        return $columns;
    }

}