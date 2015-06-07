<?php

class Example_Ex03Controller extends Sing_Controller_Action
{
    public function indexAction()
    {
        $this->view->title = 'Ex03.プラグインの実行順';
    }
}