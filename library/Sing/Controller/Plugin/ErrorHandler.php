<?php

/**
 * @category  Sing_Controller_Plugin
 * @author    t.watanabe
 * @since     2014/12/28
 */
class Sing_Controller_Plugin_ErrorHandler extends Zend_Controller_Plugin_ErrorHandler
{
    /**
     * @see Zend_Controller_Plugin_Abstract::dispatchLoopStartup
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        error_reporting(E_ALL);
        set_error_handler(array($this, 'errorHandler'));

        register_shutdown_function(function () {
            $error = error_get_last();
            if (!empty($error)) {
                $this->errorHandler($error['type'], $error['message'], $error['file'], $error['line']);
            }
        });
    }

    public function errorHandler($errno, $errstr, $errfile, $errline)
    {
        $this->controllTransaction();

        // スクリプトの致命的エラーはここで完結
        if (E_PARSE == $errno) {
            Sing_Runner::getInstance()->setStatus(Sing_Const::FAILUR);
            Sing_Log::alert('Syntax error が発生しています, ' . $errstr . " in {$errfile}({$errline})");
            exit;
        }

        $errorContext = array(
            'params' => $this->_request->getParams(),
            'errors' => array(
                'errno'   => $errno,
                'errstr'  => $errstr,
                'errfile' => $errfile,
                'errline' => $errline
            ),
        );
        Zend_Registry::set('errorContext', $errorContext);

        throw new Sing_Controller_Plugin_Exception();
    }

    private function controllTransaction()
    {
        $front = Zend_Controller_Front::getInstance();
        $dbPlugin = $front->getPlugin('Sing_Controller_Plugin_DBTransaction');

        // トランザクションが開始されていればトランザクション処理を実施
        if ($dbPlugin->inTransaction()) {
            $dbPlugin->execTransaction();
        }
    }
}