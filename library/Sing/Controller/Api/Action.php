<?php

/**
 * @category  Sing_Controller_Api
 * @author    t.watanabe
 * @since     2015/04/11
 */
abstract class Sing_Controller_Api_Action extends Sing_Controller_Action
{
    public function indexAction()
    {
        if (!Sing_Runner::isXmlHttpRequest()) {
            throw new Sing_Controller_Api_Exception('APIをXMLHttpRequestで実行していません');
        }

        try {
            $this->viewRender = false;

            $this->initialize();

            $rs = $this->execute();
            if (!is_array($rs)) {
                throw new Sing_Exception('API結果が配列ではありません');
            }

            if ($callback = arrVal($rs, 'callback')) {
                unset($rs['callback']);
            }

            $this->outputJson($rs, $callback);

        } catch (Exception $e) {
            Sing_Log::error('API実行で例外が発生しています');
            throw $e;
        }
    }

    abstract protected function execute();

    protected function initialize()
    {}

    protected function outputJson(array $data, $callback)
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8');
        if ($callback) {
            echo $callback. '('. Zend_Json::encode($data). ');';
        } else {
            echo Zend_Json::encode($data);
        }
    }
}