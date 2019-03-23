<?php

namespace User\Controller\Backend;

use Bhive\Controller\AbstractController;
use Zend\View\Model\ViewModel;
use Com\DataGrid\TinyGrid;
use Zend;

class UserController extends AbstractController
{

    function indexAction()
    {
        $sm = $this->getContainer();
        $gUsers = $sm->get('User\Grid\Users');

        $grid = $gUsers->getGrid();
        $grid->setQueryParams($this->params()->fromQuery());

        return array(
            'grid' => $grid->render()
        );
    }


    function addAction()
    {
        $sm = $this->getContainer();
        $fUser = $sm->get('User\Form\User')->build();

        $request = $this->getRequest();

        if($request->isPost())
        {
            $post = $request->getPost();
            $fUser->setData($post);

            $control = $sm->get('User\Control');
            $com = $control->saveUser($post);

            if($com->isSuccess())
            {
                $fUser->reset();
            }
            else
            {
                $this->setFormMessages($fUser, $com);
            }
        }

        #
        return array(
            'form' => $fUser
        );
    }


    function editAction()
    {
        $id = $this->params('id');

        $sm = $this->getContainer();
        $fUser = $sm->get('User\Form\User')->build();

        $request = $this->getRequest();

        if($request->isPost())
        {
            $post = $request->getPost();
            $post->id = $id;

            $fUser->setData($post);

            $control = $sm->get('User\Control');
            $com = $control->saveUser($post);

            if($com->isSuccess())
            {
                ;
            }
            else
            {
                $this->setFormMessages($fUser, $com);
            }
        }
        else
        {
            $enity = $sm->get('User\Db\User')->findByPrimarykey($id);
            if($enity)
            {
                $fUser->setData($enity->toArray());
            }
        }

        #
        return array(
            'form' => $fUser
        );
    }
}
