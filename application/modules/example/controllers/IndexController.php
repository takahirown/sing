<?php

class Example_IndexController extends Sing_Controller_Action
{
    public function indexAction()
    {
        $this->view->title = '用例 - Example';
    }
}