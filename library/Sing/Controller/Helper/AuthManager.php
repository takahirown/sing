<?php

/**
 * @category  Sing_Controller_Helper
 * @author    t.watanabe
 * @since     2015/05/05
 */
class Sing_Controller_Helper_AuthManager extends Zend_Controller_Action_Helper_Abstract
{
    public function checkAccess()
    {
        $controller = $this->getActionController();

        if ($this->isAuthenticatable($controller)) {
            if (!$component = Sing_Runner::getInstance()->getContainer()->getComponent('Authorize')) {
                throw new Sing_Controller_Helper_Exception('認証チェック用コンポーネントが見つかりません');
            }
            $component->execute();
        }
    }

    protected function isAuthenticatable(Zend_Controller_Action $controller)
    {
        return (($controller instanceof Authenticatable) && (Sing_Session::getAuthUser() == null)) ? true : false;
    }
}