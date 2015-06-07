<?php

/**
 * HTTPセッションユーティリティクラス
 *
 * @category  Sing
 * @author    t.watanabe
 * @since     2014/07/26
 */
class Sing_Session
{
    /**
     * セッション内の名前空間
     * @var string
     */
    private static $_namespace;

    /**
     * セッションを初期化、開始します。
     *
     * @param array $options                - セッションの設定情報配列
     * @return void
     */
    public static function init($options)
    {
        Zend_Session::setOptions(array(
                'name'           => $options['session_name'],
                'gc_maxlifetime' => $options['session_gc_maxlifetime'],
                'save_path'      => $options['session_save_path'],
            ));
        Zend_Session::start();

        self::$_namespace = $options['session_namespace'];

        $session = self::getSession(self::$_namespace);
        $session->setExpirationSeconds($options['session_lifetime']);
    }

    private static function getSession($namespace)
    {
        if (self::$_namespace) {
            return new Zend_Session_Namespace($namespace);
        }
        return false;
    }

    /**
     * セッションからキー指定で値を取得します。
     *
     * @param string $sessionKey            - セッションキー
     * @param mixed  $default               - 値が無い場合のデフォルト値
     * @return mixed
     */
    public static function get($sessionKey, $default = null)
    {
        $session = self::getSession(self::$_namespace);
        return isset($session->$sessionKey) ? $session->$sessionKey : $default;
    }

    /**
     * セッションにキー指定で値を設定します。
     *
     * @param string $sessionKey            - セッションキー
     * @param mixed  $value                 - 値
     * @return void
     */
    public static function set($sessionKey, $value)
    {
        $session = self::getSession(self::$_namespace);
        $session->$sessionKey = $value;
    }

    /**
     * HOP数付きでセッションに値を設定します。
     *
     * @param string $sessionKey            - セッションキー
	 * @param mixed  $value                 - 値
	 * @param int    $counts                - HOP数、セットしたHOP数分セッショから値を取り出すと自動で値を削除
	 * @return void
     */
    public static function setByHops($sessionKey, $value, $counts)
    {
        $session = self::getSession(self::$_namespace);
        $session->setExpirationHops($counts, $sessionKey);
        $session->$sessionKey = $value;
    }

    /**
     * セッションにキー指定で値があるかどうか
     *
     * @param string $sessionKey            - セッションキー
     * @return 存在すれば TRUE
     */
    public static function exists($sessionKey)
    {
        $session = self::getSession(self::$_namespace);
        return isset($session->$sessionKey);
    }

    /**
     * セッションからキー指定で値を削除します。
     *
     * @param string $sessionKey            - セッションキー
     * @return 削除した場合 TRUE、存在していない場合 FALSE
     */
    public static function remove($sessionKey)
    {
        $session = self::getSession(self::$_namespace);
        if (isset($session->$sessionKey)) {
            unset($session->$sessionKey);
            return true;
        }
        return false;
    }

    /**
     * セッションの有効期限を再設定します。
     *
     * @param int $sec                      - 更新する秒数
     */
    public static function updateExpirationSeconds($sec)
    {
        $session = self::getSession(self::$_namespace);
        $session->setExpirationSeconds($sec);
    }

    /* ------------------------------------------------------------------------------------------------------------- */

    /**
     * 特定のユーザ情報をログイン済みとして扱います。
     *
     * @param mixed $userData               - ログイン済みとして扱うユーザー情報
     * @param string $rememberMeSeconds     - ログイン状態を記録する秒数、クッキーの保存期間で指定しないとセッション扱い（ブラウザ閉じで切れる）
     *                                        クッキーの保存期間はアクセスの度に延長される事はなく、期限と共にクライアントから削除されます
     * @return void
     */
    public static function setAuth($userData, $rememberMeSeconds = null)
    {
        self::set(SG_SESS_LOGIN_USER, $userData);
        if ($rememberMeSeconds) {
            Zend_Session::rememberMe($rememberMeSeconds);
        } else {
            Zend_Session::forgetMe();
        }
    }

    /**
     * ログイン済みの情報を破棄します。クッキーの保存期間はセッション扱いになります。
     *
     * @return void
     */
    public static function destroyAuth()
    {
        Zend_Session::destroy();
    }

    /**
     * ログイン済みのユーザ情報を取得します。 $name を指定した場合はユーザ情報の中の個別の情報のみ取得できます。
     * $name を指定しない場合はユーザ情報をすべて取得できます。
     *
     * $closure にログイン済ユーザのアカウント生存確認用無名関数をセットした場合は、関数を実施します。
     *
     * @param string $name                  - ユーザ情報の中の情報名
     * @param Closure $closure              - アカウント生存確認用の無名関数
     * @return mixed
     */
    public static function getAuthUser($name = null, $closure = null)
    {
        // アカウント生存確認用の無名関数がセットされていれば実施
        if ($closure != null) {
            $closure();
        }

        if ($name !== null) {
            $user = self::get(SG_SESS_LOGIN_USER);
            return arrVal($user, $name);
        }
        return self::get(SG_SESS_LOGIN_USER);
    }

    /* ------------------------------------------------------------------------------------------------------------- */

    /**
     * データストアからデータを取得します。$name がセットされていなければデータストアのすべてが返されます。
     * $name を指定した場合はデータストアから $name を指定してデータを取得します。
     *
     * $name が存在しない、且つ $default がセットされている場合 NULL の代わりに $default を返します。
     *
     * @param string $name                  - データストアのパラメータ名
     * @param string $default               - デフォルト値
     * @return mixed
     */
    public static function store($name = null, $default = null)
    {
        $dataStore = self::get(SG_SESS_DATA_STORE);
        if ($name === null) {
            return $dataStore;
        }
        return (isset($dataStore[$name])) ? $dataStore[$name] : $default;
    }

    /**
     * データストアにデータを登録します。データストア内の保管場所は1つで、常に上書きで登録していきます。
     * clearStore() でデータストアをすべてクリアします。
     *
     * 画面遷移のタイミングで POST リクエストが無ければ自動でデータストアをクリアします。
     *
     * @param mixed $value
     * @return void
     */
    public static function registerStore($value)
    {
        self::set(SG_SESS_DATA_STORE, $value);
    }

    /**
     * データストアをクリアします。
     *
     * @return void
     */
    public static function clearStore()
    {
        self::remove(SG_SESS_DATA_STORE);
    }
}