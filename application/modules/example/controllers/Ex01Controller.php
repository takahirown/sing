<?php

class Example_Ex01Controller extends Sing_Controller_Action
{
    public function indexAction()
    {
        $this->view->title = 'Ex01.トークンを利用したCSRF（クロスサイトリクエストフォージェリ）対策';
    }
}