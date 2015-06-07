<?php

/**
 * 一般的に利用する文字列処理をまとめた文字列ユーティリティクラス
 *
 * @category  Sing
 * @author    t.watanabe
 * @since     2015/03/28
 */
class Sing_String
{
    /**
     * 文字列内に文字列が含まれているか検査します。
     *
     * @param string $target           - 検索を行う文字列
     * @param string $needle           - 含まれているか判定する文字列
     * @param number $offset           - 文字列内での検索開始位置、デフォルト0
     * @return boolean $needleが含まれていた場合に TRUE を返します
     */
    public static function exist($target, $needle, $offset = 0)
    {
        return (strpos($target, $needle, $offset) !== false);
    }

    /**
     * 文字列を文字列により分割し配列で返します。
     *
     * @param string $target
     * @param string $delimiter
     * @return array $targetの内容を$delimiterで分割した文字列の配列
     */
    public static function divide($target, $delimiter)
    {
        return explode($delimiter, $target);
    }

    /**
     * 文字列がからかどうか検査します。
     *
     * @param string $target
     * @return boolean 空の場合に TRUE を返します
     */
    public static function blank($target)
    {
        return empty($target);
    }

    /**
     * カンマ区切りの文字列を配列に変換して返します。Key:Value形式の場合は連想配列として変換します。
     *
     * @param string $target           - 対象文字列
     * @return array 返還後の配列
     */
    public static function convertArray($target)
    {
        $result = array();
        $elements = explode(',', $target);
        foreach ($elements as $element) {
            if (strpos($element, ':') === false) {
                $result[] = self::convertInteger($element);
            } else {
                $arrKV = explode(':', $element);
                $result[$arrKV[0]] = self::convertInteger($arrKV[1]);
            }
        }
        return $result;
    }

    /**
     * 文字列が数字または数値形式の文字列である場合、数値型に変換して返します。
     *
     * @param string $target           - 対象文字列
     * @return mixed 数値型の場合は int 変換された値、それ以外は元の文字列
     */
    public static function convertInteger($target)
    {
        return is_numeric($target) ? (int)$target : $target;
    }
}