<?php

class Example_Ex02Controller extends Sing_Controller_Action
{
    /**
     * @var Model_ExampleEx02
     */
    protected $model;

    public function indexAction()
    {
        $this->view->title = 'Ex02.モデルオブジェクトの自動生成';

        //var_dump($this->model);
    }
}