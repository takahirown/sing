<?php

/**
 * @category  Sing_Controller
 * @author    t.watanabe
 * @since     2014/07/26
 */
class Sing_Controller_Error extends Sing_Controller_Action
{
    /**
     * 処理対象が見つからないエラーの場合
     */
    const ERROR_NOT_FOUND = 0;

    /**
     * スクリプトエラーの場合
     */
    const ERROR_SCRIPT = 1;

    /**
     * 例外がスローされている場合
     */
    const ERROR_EXCEPTION = 2;

    /**
     * シェルがコマンドライン実行ではない場合
     */
    const ERROR_NONE_CLI = 3;

    /**
     * APIがXMLHTTPRequestで実行されていない場合
     */
    const ERROR_NONE_XMLHTTPREQUEST = 4;

    protected $_messageTemplates = array(
        'ERROR_NOT_FOUND_TITLE'  => 'お探しのページは見つかりません。',
        'ERROR_SCRIPT_TITLE'     => '問題が発生しています。',
        'ERROR_SCRIPT_MESSAGE'   => 'スクリプトエラーが発生しています。',
        'ERROR_EXCEPTION_TITLE'  => '問題が発生しています。',
        'ERROR_NONE_CLI_TITLE'   => '問題が発生しています。',
        'ERROR_NONE_CLI_MESSAGE' => '許可されないアクセスです。',
        'ERROR_UNKNOWN_TITLE'    => '問題が発生しています。',
        'ERROR_UNKNOWN_MESSAGE'  => '想定外のエラー制御が発生しています。',
        'ERROR_NONE_API_TITLE'   => '問題が発生しています。',
        'ERROR_NONE_API_MESSAGE' => '許可されないアクセスです。',
    );

    public function errorAction()
    {
        Sing_Runner::getInstance()->setStatus(Sing_Const::FAILUR);
        $errors = $this->_getParam('error_handler');
        $exception = $errors['exception'];

        $errorNo = Sing_Security::uniqRandomString(6);
        $this->view->errorNo = $errorNo;

        switch (Sing_Controller_ErrorUtil::getType($errors)) {

            case self::ERROR_NOT_FOUND:

                Sing_Log::notice('指定されたページが見つかりません, ' . $this->getRequest()->getRequestUri() . ', '.
                        $exception->getMessage() . ', ERROR_NO:'. $errorNo);

                $this->view->title = $this->_messageTemplates['ERROR_NOT_FOUND_TITLE'];
                $this->getResponse()->setHttpResponseCode(404);
                $this->changeScript('404');
                break;

            case self::ERROR_SCRIPT:

                $err = Zend_Registry::get('errorContext');

                Sing_Log::alert('スクリプトエラーが発生しています, ' . Sing_Controller_ErrorUtil::getErrorString($err['errors']['errno']) . ':' .
                        $err['errors']['errstr'] . ', ERROR_NO:'. $errorNo . "\n" . $exception->getTraceAsString());

                $this->view->title = $this->_messageTemplates['ERROR_SCRIPT_TITLE'];
                $this->view->message = $this->_messageTemplates['ERROR_SCRIPT_MESSAGE'];
                $this->getResponse()->setHttpResponseCode(500);
                break;

            case self::ERROR_EXCEPTION:

                Sing_Log::alert($exception->getMessage() . ', ERROR_NO:'. $errorNo . "\n" .
                        get_class($exception) . ', ' . $exception->getFile() . "({$exception->getLine()})\n" .
                        $exception->getTraceAsString());

                $this->view->title = $this->_messageTemplates['ERROR_EXCEPTION_TITLE'];
                $this->view->message = $exception->getMessage();
                $this->getResponse()->setHttpResponseCode(500);
                break;

            case self::ERROR_NONE_CLI:

                Sing_Log::alert($exception->getMessage().', '. $this->getRequest()->getRequestUri() . ', ERROR_NO:'. $errorNo);

                $this->view->title = $this->_messageTemplates['ERROR_NONE_CLI_TITLE'];
                $this->view->message = $this->_messageTemplates['ERROR_NONE_CLI_MESSAGE'];
                $this->getResponse()->setHttpResponseCode(500);
                break;

            case self::ERROR_NONE_XMLHTTPREQUEST:

                Sing_Log::alert($exception->getMessage().', '. $this->getRequest()->getRequestUri() . ', ERROR_NO:'. $errorNo);

                $this->view->title = $this->_messageTemplates['ERROR_NONE_API_TITLE'];
                $this->view->message = $this->_messageTemplates['ERROR_NONE_API_MESSAGE'];
                $this->getResponse()->setHttpResponseCode(500);
                break;

            default:

                Sing_Log::alert('原因不明のエラーが発生しています, ERROR_NO:'. $errorNo);

                $this->view->title = $this->_messageTemplates['ERROR_UNKNOWN_TITLE'];
                $this->view->message = $this->_messageTemplates['ERROR_UNKNOWN_MESSAGE'];
                $this->getResponse()->setHttpResponseCode(500);
        }

        if (Sing_Runner::isCli() || Sing_Runner::isXmlHttpRequest()) {
            $this->viewRender = false;
        }
    }
}