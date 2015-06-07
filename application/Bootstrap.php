<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initAutoload()
    {
        $autoloader = new Sing_Autoloader(array(
                'namespace' => '',
                'basePath'  => APPLICATION_PATH,
        ));
        return $autoloader;
    }

    protected function _initApplication()
    {
        $sing = Sing_Runner::getInstance();
        $sing->setOptions($this->getOptions());
        $sing->getContainer()->addComponent(new Component_Authorize());
        $sing->run('app-config');

        Zend_Controller_Action_HelperBroker::addHelper(new Component_AuthManager());
    }
}