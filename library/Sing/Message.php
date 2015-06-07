<?php

/**
 * メッセージユーティリティクラス
 *
 * @category   Sing
 * @author     t.watanabe
 * @since      2014/07/26
 */
final class Sing_Message
{
    /** 未定義 */
    const MESSAGE_NOT_FOUND = 'MESSAGE_NOT_FOUND';

    /** メッセージリソース */
    private static $_msg = null;

    /**
     * 当ユーティリティを初期化します。
     *
     * @param string $lang
     */
    public static function init($lang = 'ja')
    {
        self::$_msg = array();

        $mesFile = APPLICATION_PATH. '/configs/message.'. $lang. '.php';
        if (file_exists($mesFile)) {
            self::$_msg = require($mesFile);
        }
    }

    /**
     * キー指定でメッセージを取得します。
     *
     * %s、%d でメッセージの置換えが可能。$options に配列でセット。
     *
     * @param string $key
     * @param array  $options メッセージ置換配列
     * @return mixed
     */
    public static function get($key, $options = null)
    {
        if (isset(self::$_msg[$key])) {
            $msg = self::$_msg[$key];

            if (!is_array($msg)) {

                //通常のメッセージ

                if (!is_null($options)) {
                    if (!is_array($options)) {
                        $options = array($options);
                    }
                    $msg = vsprintf($msg, $options);
                }
            }

            return $msg;
        }

        return self::MESSAGE_NOT_FOUND;
    }
}