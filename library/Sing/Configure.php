<?php

/**
 * 設定値を管理するクラス。実行時に設定情報を管理するために使われます。
 *
 * @category  Sing
 * @author    t.watanabe
 * @since     2014/12/26
 */
class Sing_Configure
{
    /**
     * 設定値を保管する配列
     * @var array
     */
    private static $_values = array();

    /**
     * application.ini の app. で始まる設定値配列を内部で保管します。
     *
     * @param array $appConfig         - application.ini の app. で始まる設定値配列
     * @return void
     */
    public static function init(array $appConfig)
    {
        foreach ($appConfig as $name => $value) {
            self::$_values[$name] = $value;
        }
    }

    /**
     * ファイル名キーを指定して設定ファイルの設定値を内部で保管します。
     *
     * 設定ファイルは APPLICATION_PATH /configs 配下に拡張子 .php で配置してください。ファイル名は自由です。
     * 本メソッドは何度でもコールできます。その際の設定値は常に上書きされます。
     *
     * application.ini の app. で始まる設定値が一番最初に読み込まれます。
     * 以降で同名の設定値が読み込まれると application.ini の設定値が上書きされることに注意してください。
     *
     * @param string $key              - ファイル名キー（拡張子を除いたファイル名）
     * @return void
     * @throws Zend_Exception 設定ファイルが存在しない場合
     */
    public static function load($key)
    {
        $configFile = APPLICATION_PATH. '/configs/'. $key. '.php';
        if (!file_exists($configFile)) {
            throw new Zend_Exception('設定ファイルが存在しない  パス情報：'. $configFile);
        }
        $config = require($configFile);
        foreach ($config as $name => $value) {
            self::$_values[$name] = $value;
        }
    }

    /**
     *
     *
     * @param string $name
     * @return mixed
     */
    public static function read($name = null)
    {
        if ($name === null) {
            return self::$_values;
        }
        return self::$_values[$name];
    }
}