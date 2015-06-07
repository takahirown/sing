<?php

/**
 * @category  Sing
 * @author    Takahiro
 * @since     2015/05/05
 */
class Sing_Const
{
    const SUCCESS = 0;

    const FAILUR = 1;

    const TYPE_ARRAY = 0;

    const TYPE_JSON = 1;
}


/* Sing Framework 標準インターフェース、及びクラス
 ---------------------------------------------------------------------------------------------------------------------*/

/**
 * 管理者を表すマーカーインターフェース
 *
 * @category Sing
 * @author   Takahiro
 */
interface Administratable
{}

/**
 * ログイン認証を表すマーカーインターフェース
 *
 * @category Sing
 * @author   Takahiro
 */
interface Authenticatable
{}


/* Sing Framework のグルーバル関数
---------------------------------------------------------------------------------------------------------------------*/

/**
 * 配列から名前を指定して値を取得します。名前の存在チェックを行い存在しない場合は NULL を返します。
 *
 * @param array $arr              - 値を保持している配列
 * @param string $name            - 配列内の名前
 * @return mixed
 */
function arrVal($arr, $name)
{
    if (!is_array($arr)) {
        return null;
    }
    return (isset($arr[$name])) ? $arr[$name] : null;
}


/* Sing Framework のグローバル定数
 ---------------------------------------------------------------------------------------------------------------------*/

/** ディレクトリ区切り文字 */
define('DS', DIRECTORY_SEPARATOR);

/** トランザクショントークン名 */
define('SG_TRANSACTION_TOKEN', 'secureToken');

/** より上（該当を含まない） */
define('SG_OVER', 2);

/** 以上（該当を含む） */
define('SG_AND_OVER', 1);

/** 入力チェックエラー有り */
define('SG_VALID_ERROR', true);

/** PHP実行エラーの例外コード */
define('SG_EXCEPTION_CODE_PHP_ERROR', 900);

/* 通信
 --------------------------------------------------------*/

/** 通信タイプ 非同期 */
define('SG_COM_ASYNCHRONOUS', 'SG_COM_ASYNCHRONOUS');

/** 通信タイプ 同期 */
define('SG_COM_SYNCHRONOUS', 'SG_COM_SYNCHRONOUS');

/** 非同期のレスポンスタイプ JSON */
define('SG_JSON', 0);

/** 非同期のレスポンスタイプ JSONP */
define('SG_JSONP', 1);

define('SG_RUNNING_MODE_WEB', 0);
define('SG_RUNNING_MODE_AJAX', 1);
define('SG_RUNNING_MODE_CLI', 2);

/* SESSION キー
 --------------------------------------------------------*/

/** PHP実行エラーの内容 */
define('SG_SESS_ERROR_CONTEXT', 'sing.session.error.context');

/** トランザクション・トークン */
define('SG_SESS_TOKEN_KEY', 'sing.session.transaction.token');

/** ログインユーザ */
define('SG_SESS_LOGIN_USER', 'sing.session.login.user');

/** データストア領域 */
define('SG_SESS_DATA_STORE', 'sing.session.data.store');

/* 画面通知タイプ
 --------------------------------------------------------*/

/** 成功通知 */
define('SG_NOTICE_SUCCESS', 0);

/** 警告通知 */
define('SG_NOTICE_WARNING', 1);

/** エラー通知 */
define('SG_NOTICE_ERROR', 2);

/** 一般通知 */
define('SG_NOTICE_INFO', 3);

/* ViewScript 定数
 --------------------------------------------------------*/

/** パラメータの出力で値をエスケープする */
define('SG_ESCAPE', true);

/** select タグの option タグを降順で出力する */
define('SG_DESC', true);

/** select タグの option タグを昇順で出力する */
define('SG_ASC', false);

/** ラジオボタンを出力 */
define('SG_RADIO', 1);

/** チェックボックスを出力 */
define('SG_CHECKBOX', 2);

/** セレクトボックスを出力 */
define('SG_SELECTBOX', 3);

/** チェックボックス（インライン）を出力 */
define('SG_CHECKBOX_INLINE', 4);

/** ラジオボタン（インライン）を出力 */
define('SG_RADIO_INLINE', 5);

/** ラジオボタン（インライン）を出力 */
define('SG_RADIO_BUTTON', 6);