<?php

namespace Front\Controller;

use Bhive\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Com\DataGrid\TinyGrid;
use Zend;

class IndexController extends AbstractController
{

    function indexAction()
    {
        $container = $this->getContainer();
        $adapter = $container->get('adapter');
        $adapter->query("select version()");
    }



    function gridAction()
    {

        $grid = new TinyGrid(null, null, $_GET);


        $columns = array(   

            'id' => array(
                'header' => array(
                    'label' => 'ID',
                    'sort' => true,
                    'filter' => 1,
                ),
                'cell' => array(
                  
                )
            ),

            'title' => array(
                'header' => array(
                    'label' => 'Title',
                    'sort' => true,
                    'filter' => 1,
                ),
                'cell' => array(
                  
                )
            ),

            'status' => array(
                'header' => array(
                    'label' => 'Status',
                    'sort' => true,
                    'filter' => 1,
                ),
                'cell' => array(
                    
                )
            ),

            'created_on' => array(
                'header' => array(
                    'label' => 'Date',
                    'sort' => true,
                    'filter' => 1,
                ),
                'cell' => array(
                    'type' => 'relative_date',
                )
            ),

            'action' => array(
                'header' => array(
                    'label' => 'Action',
                    'attributes' => array('style' => 'width:115px;'),
                ),
                'cell' => array(

                    'callback' => function($value, $row, $field, $config) {

                        return 21;
                    }, // 
                )
            ),
        );

        $grid->setColumns($columns);


        $container = $this->getContainer();
        $adapter = $container->get('adapter');

        $select = new Zend\Db\Sql\Select();
        $select->from('form');
        
        $grid->setSource($select, $adapter);

        #echo '--';
        echo $grid->render();
        #echo '!--';
        exit;
    }
}