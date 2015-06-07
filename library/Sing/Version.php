<?php

/**
 * @category   Sing
 * @author     t.watanabe
 * @since      2014/11/29
 */
final class Sing_Version
{
    const VERSION = '0.15.05';

    /**
     * 動作環境が期待する PHP バージョン以降かどうか比較する
     *
     * @return boolean TRUE:期待するバージョン以降
     */
    public static function comparePhpVersion()
    {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            return true;
        }
        return false;
    }
}
