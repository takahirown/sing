<?php

class Sing_Controller_Router_Cli extends Zend_Controller_Router_Abstract implements Zend_Controller_Router_Interface
{
    public function route(Zend_Controller_Request_Abstract $dispatcher)
    {
        $arguments = Zend_Registry::get('argv');

        if (count($arguments) < 2) {
            throw new Sing_Controller_Exception('シェル実行の引数でコントローラ情報が指定されていません');
        }

        list($module, $controller) = Sing_String::divide($arguments[1], '_');
        return $dispatcher->setModuleName($module)->setControllerName($controller)
            ->setActionName('index')->setParam('args', $arguments[2]);
    }

    public function assemble($userParams, $name = null, $reset = false, $encode = true)
    {}
}
