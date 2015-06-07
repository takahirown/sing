<?php

/**
 * @category  Sing_Controller_Plugin
 * @author    t.watanabe
 * @since     2014/12/28
 */
class Sing_Controller_Plugin_DBTransaction extends Zend_Controller_Plugin_Abstract
{
    private $startTransactionFlg = false;

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $front = Zend_Controller_Front::getInstance();
        $dbAdapter = $front->getParam('bootstrap')->getPluginResource('db')->getDbAdapter();
        Zend_Registry::set('db', $dbAdapter);
    }

    public function beginTransaction()
    {
        if ($this->startTransactionFlg === false) {
            $db = Zend_Registry::get('db');
            $db->beginTransaction();
            $this->startTransactionFlg = true;
        }
    }

    public function inTransaction()
    {
        return $this->startTransactionFlg;
    }

    public function __destruct()
    {
        if ($this->startTransactionFlg) {
            $this->execTransaction();
        }
    }

    public function execTransaction()
    {
        $db = Zend_Registry::get('db');
        if ('error' == $this->getRequest()->getControllerName()) {
            $db->rollBack();
        } else {
            $db->commit();
        }
    }
}
