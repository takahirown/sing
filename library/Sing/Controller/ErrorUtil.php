<?php

/**
 * @category  Sing_Controller
 * @author    t.watanabe
 * @since     2015/04/12
 */
class Sing_Controller_ErrorUtil extends Sing_Controller_Action
{
    public static function getType($errors)
    {
        $exception = $errors['exception'];
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                return Sing_Controller_Error::ERROR_NOT_FOUND;
            default:
                if ($exception instanceof Sing_Controller_Plugin_Exception) {
                    return Sing_Controller_Error::ERROR_SCRIPT;
                } else if ($exception instanceof Sing_Controller_Shell_Exception) {
                    return Sing_Controller_Error::ERROR_NONE_CLI;
                } else if ($exception instanceof Sing_Controller_Api_Exception) {
                    return Sing_Controller_Error::ERROR_NONE_XMLHTTPREQUEST;
                } else {
                    return Sing_Controller_Error::ERROR_EXCEPTION;
                }
        }
    }

    /**
     * エラーレベルからエラー文字列を取得します。
     *
     * @param int $level
     * @return string
     */
    public static function getErrorString($level)
    {
        switch($level) {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
            default:
                return 'E_UNKNOWN';
        }
    }
}