<?php

class IndexController extends Sing_Controller_Action
{
    public function indexAction()
    {
        $variables = array(
            'title' => 'hello, world',
            'memo'  => 'with Sing ' . Sing_Version::VERSION,
        );

        $this->assign($variables);
    }
}