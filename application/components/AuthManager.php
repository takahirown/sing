<?php

/**
 * @author  t.watanabe
 * @since   2015/05/19
 */
class Component_AuthManager extends Zend_Controller_Action_Helper_Abstract
{
    public function execute()
    {
        $controller = $this->getActionController();

        if ($this->isUnlogin($controller)) {
            header('location: /login/');
            exit;
        }
    }

    protected function isUnlogin(Zend_Controller_Action $controller)
    {
        return (($controller instanceof Authenticatable) && (Sing_Session::getAuthUser() == null)) ? true : false;
    }
}