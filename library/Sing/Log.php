<?php

/**
 * ログユーティリティクラス。
 *
 * @category  Sing
 * @author    t.watanabe
 * @since     2014/07/26
 */
class Sing_Log
{
    /**
     * @var Zend_Log
     */
    private static $_logger;

    /**
     * 現在のログファイルパス。
     */
    private static $_filePath;

    /**
     * ログユーティリティの初期設定を行います。
     *
     * @param string $path
     * @param string $filename
     * @param int $level
     * @param int $permission
     * @throws Zend_Exception
     */
    public static function init($path, $filename, $level = 7, $permission = null)
    {
        $filename = strtr($filename, array(
                '[YYYY]' => date('Y'),
                '[MM]'   => date('m'),
                '[N]'    => Sing_Date::getWeekNumber(),
            ));
        $filePath = $path. DS. $filename;

        if ($permission != null) {
            chmod($filePath, $permission);
        }

        $stream = @fopen($filePath, 'a', false);
        if (!$stream) {
            throw new Zend_Exception('ログファイルに書き込みに失敗しました。 : '. $filePath);
        }

        $writer = new Zend_Log_Writer_Stream($stream);
        $writer->addFilter(new Zend_Log_Filter_Priority((int)$level));
        $writer->setFormatter(new Zend_Log_Formatter_Simple('['.date("Y-m-d H:i:s").'][%priorityName%]%message%' . PHP_EOL));

        self::$_logger = new Zend_Log($writer);
        self::$_filePath = $filePath;
    }

    /**
     * 現在のログファイルのパーミッションを設定します。
     *
     * @param int $permission
     * @return boolean TRUE:成功
     */
    public static function chmod($permission)
    {

        return chmod(self::$_filePath, $permission);
    }

    /**
     * デバッグ (Debug): デバッグメッセージレベルでログ出力します。
     *
     * @param string $msg
     */
    public static function debug($msg)
    {
        self::write(debug_backtrace(), $msg, Zend_Log::DEBUG);
    }

    /**
     * 情報 (Informational): 情報メッセージレベルでログ出力します。
     *
     * @param string $msg
     */
    public static function info($msg)
    {
        self::write(debug_backtrace(), $msg, Zend_Log::INFO);
    }

    /**
     * 注意 (Notice): 通常動作ですが、注意すべき状況レベルでログ出力します。
     *
     * @param string $msg
     */
    public static function notice($msg)
    {
        self::write(debug_backtrace(), $msg, Zend_Log::NOTICE);
    }

    /**
     * 警告 (Warning): 警告レベルでログ出力します。
     *
     * @param string $msg
     */
    public static function warn($msg)
    {
        self::write(debug_backtrace(), $msg, Zend_Log::WARN);
    }

    /**
     * エラー (Error): エラーレベルでログ出力します。
     *
     * @param string|Exception $msg
     */
    public static function error($msg)
    {
        self::write(debug_backtrace(), $msg, Zend_Log::ERR);
    }

    /**
     * 危機 (Critical): 危機的な状況レベルでログ出力します。
     *
     * @param string $msg
     */
    public static function crit($msg)
    {
        self::write(debug_backtrace(), $msg, Zend_Log::CRIT);
    }

    /**
     * 警報 (Alert): 至急対応レベルでログ出力します。
     *
     * @param string $msg
     */
    public static function alert($msg)
    {
        self::write(debug_backtrace(), $msg, Zend_Log::ALERT);
    }

    /**
     * 緊急事態 (Emergency): システムが使用不可能レベルでログ出力します。
     *
     * @param string $msg
     */
    public static function emerg($msg)
    {
        self::write(debug_backtrace(), $msg, Zend_Log::EMERG);
    }

    /**
     * ログ出力.
     *
     * @param array $backtraces
     * @param string $msg
     * @param int $level
     */
    private static function write($backtraces, $msg, $level)
    {
        if (self::$_logger) {
            $ip = (isset($_SERVER["REMOTE_ADDR"])) ? $_SERVER["REMOTE_ADDR"] : '0.0.0.0';
            $output  = '['. $ip . '] ';
            $msg = (is_array($msg) || is_object($msg)) ? json_encode($msg) : $msg;
            if ($backtraces === null) {
                $output .= $msg;
            } else {
                $output .= $backtraces[1]['class']. '('. $backtraces[0]['line']. ') '. $msg;
            }
            $output = mb_convert_encoding($output, 'UTF-8', 'auto');
            self::$_logger->log($output, $level);
        }
    }
}
