<?php

/**
 * Zend_View の拡張クラス
 *
 * ビュースクリプト内で利用する拡張メソッドを定義しています。
 * コントローラから $options、$params、$contentCss、$errors、$notices のメンバ変数に必要な情報をセットしてください。
 *
 * @category   Sing
 * @author     t.watanabe
 * @since      2014/12/07
 */
class Sing_View extends Zend_View
{
    /**
     * プルダウン(select)、ラジオボタン(radio)、チェックボックス(checkbox)の選択肢
     * @var array 選択肢名をキーとした値と表示文字列の連想配列の二次元配列
     */
    public $options = null;

    /**
     * リクエストパラメータ
     * @var array
     */
    public $params = null;

    /**
     * エラー情報
     * @var array 通知レベルとメッセージ本文からなる連想配列を保持する配列
     */
    public $errors = null;

    /**
     * 通知情報
     * @var array 通知レベルとメッセージ本文からなる連想配列
     */
    public $notices = null;

    /**
     * 検索条件
     * @var array
     */
    public $searchConds;

    /**
     * マジック・プロパティ格納用配列
     * @var array
     */
    private $data = array();

    //public $escape

    /* ------------------------------------------------------------------------------------------------------------- */

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        return null;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }

    /* ------------------------------------------------------------------------------------------------------------- */

    /**
     * ビュースクリプト内でパラメータを取得します。パラメータの取得順は  リクエスト ＞ セッション(Store) ＞ 検索条件 ＞ デフォルト値 になります。
     * $isEscape を TRUE でコールすると htmlspecialchars によるエスケープ処理を行います。
     *
     * @param string $paramName        - パラメータ名
     * @param mixed $default           - パラメータ値が無いときのデフォルト値、チェックボックスで配列を指定できます
     * @param boolean $isEscape        - エスケープ処理を行うかどうか
     */
    public function param($paramName, $default = null, $isEscape = true)
    {
        $value = $this->_value($paramName);

        // パラメータがnullのときデフォルト値を使う
        if (is_null($value) && !is_null($default)){
            $value = $default;
        }
        echo ($isEscape) ? $this->h($value) : $value;
    }

    /**
     * 指定した値をエスケープして返却します。
     *
     * @param string $value
     * @return string
     */
    public function h($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, mb_internal_encoding());
    }

    /**
     * 指定した通知レベルでセットされたメッセージを出力します。
     */
    public function notices()
    {
        foreach ($this->notices as $notice) {
            list($level, $message) = $notice;
            switch ($level) {
                case SG_NOTICE_SUCCESS:
                    echo '<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-ok" style="font-size:18px"></span> '. $message. '</div>';
                    break;
                case SG_NOTICE_INFO:
                    echo '<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-ok" style="font-size:18px"></span> '. $message. '</div>';
                    break;
                case SG_NOTICE_WARNING:
                    echo '<div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-warning-sign" style="font-size:18px"></span> '. $message. '</div>';
                    break;
                case SG_NOTICE_ERROR:
                    echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-remove-circle" style="font-size:18px"></span> '. $message. '</div>';
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * 要素毎のエラーメッセージを出力します。
     * コントローラの入力値バリデートチェックなどで名前を付けてエラーメッセージを登録し、ビュースクリプト内で登録名を指定して出力します。
     *
     * @param string $paramName        - エラーの登録名
     * @return void
     */
    public function error($paramName)
    {
        if (isset($this->errors[$paramName])) {
            echo '<div class="control-label error">'. $this->errors[$paramName]. '</div>';
        }
    }

    /**
     * セレクトボックス、チェックボックス、ラジオボタンの選択肢を出力します。
     * デフォルト値を複数渡す際は１次元配列が指定できます。
     *
     * @param string $name             - 選択肢の登録名
     * @param int $type                - SG_SELECTBOX:セレクトボックス | SG_CHECKBOX(SG_CHECKBOX_INLINE):チェックボックス | SG_RADIO(SG_RADIO_INLINE):ラジオボタン
     * @param mixed $default           - デフォルトチェックの値（任意）
     * @param string $desc             - SG_DESC:降順 | SG_ASC:昇順 （任意）
     * @return void
     */
    public function multiOptions($name, $type, $default = null, $desc = false)
    {
        // コントローラでセットされた選択肢データ分繰り返し
        foreach ($this->options as $optionKey => $optionValues) {
            if ($name == $optionKey) {

                // 選択肢をキーで降順ソート
                if ($desc) {
                    krsort($optionValues);
                }

                $idx = 1;
                foreach ($optionValues as $outValue => $outText) {


                    $selected = '';
                    $actived = '';
                    if ($this->_value($name) != null) { // パラメータが取れた場合

                        $param = $this->_value($name);

                        if (SG_CHECKBOX == $type || SG_CHECKBOX_INLINE == $type) {
                            // チェックボックスはリクエストパラメータが配列になるため回す
                            foreach ($param as $v) {
                                if ($outValue == $v) {
                                    $selected = ' checked="checked"';
                                    break;
                                }
                            }
                        } else if (SG_RADIO == $type || SG_RADIO_INLINE == $type) {
                            $selected = ($outValue == $param) ? ' checked="checked"' : '';
                        } else if (SG_RADIO_BUTTON == $type ) {
                            $actived = ($outValue == $param) ? ' active' : '';
                            $selected = ($outValue == $param) ? ' checked="checked"' : '';
                        } else if (SG_SELECTBOX == $type) {
                            $selected = ($outValue == $param) ? ' selected' : '';
                        }

                    } else if (is_array($default)) { //デフォルトが配列で複数の場合

                        foreach ($default as $defaultTarget) {
                            if ($outValue == $defaultTarget) {
                                $selected = ' checked="checked"';
                                if(SG_SELECTBOX == $type){
                                    $selected = 'selected';
                                }
                                break;
                            }
                        }

                    } else if ($default != null && $outValue == $default) { //デフォルトが単数の場合
                        $selected = ' checked="checked"';
                        if(SG_SELECTBOX == $type){
                            $selected = 'selected';
                        } elseif (SG_RADIO_BUTTON == $type ) {
                            $actived = ' active';
                        }
                    }

                    if (SG_CHECKBOX == $type) {
                        echo '<div class="checkbox"><label><input type="checkbox" value="'. $outValue. '" id="'. $name. '_'. $idx. '" name="'.
                                $name. '[]" '. $selected. '>'. $outText. '</label></div>';
                    } else if (SG_CHECKBOX_INLINE == $type) {
                        echo '<div class="checkbox-inline"><label><input type="checkbox" value="'. $outValue. '" id="'. $name. '_'. $idx. '" name="'.
                                $name. '[]" '. $selected. '>'. $outText. '</label></div>';
                    } else if (SG_RADIO == $type) {
                        echo '<div class="radio"><label><input type="radio" value="'. $outValue. '" id="'. $name. '_'. $idx. '" name="'.
                                $name. '" '. $selected. '>'. $outText. '</label></div>';
                    } else if (SG_RADIO_INLINE == $type) {
                        echo '<div class="radio-inline"><label><input type="radio" value="'. $outValue. '" id="'. $name. '_'. $idx. '" name="'.
                                $name. '" '. $selected. '>'. $outText. '</label></div>';
                    } else if (SG_RADIO_BUTTON == $type) {
                            echo '<label class="btn btn-default '.  $actived . '"><input type="radio" value="'. $outValue. '" id="'. $name. '_'. $idx. '" name="'.
                                $name. '" autocomplete="off" '. $selected. '>'. $outText. '</label>';
                    } else if (SG_SELECTBOX == $type) {
                        echo '<option value="'. $outValue. '"'. $selected. '>'. $outText. '</option>';
                    }

                    $idx++;
                }
                return;
            }
        }
    }

    public function selectedOption($name)
    {
        foreach ($this->options as $optionKey => $optionValues) {
            if ($name == $optionKey) {
                foreach ($optionValues as $outValue => $outText) {
                    if (isset($this->params[$name])) {
                        // チェックボックスは値が配列になるため回す
                        $param = $this->params[$name];

                        if (is_array($param)) {
                            foreach ($param as $v) {
                                if ($outValue == $v) {
                                    echo '<span class="control-label">'. $outText. '</span>';
                                    echo '<input type="hidden" name="'. $name. '[]" value="'. $outValue. '" /><br />';
                                }
                            }
                        } else {
                            if ($outValue == $param) {
                                echo '<span class="control-label">'. $outText. '</span>';
                                echo '<input type="hidden" name="'. $name. '" value="'. $outValue. '" />';
                                return;
                            }
                        }


                    }
                }
                return;
            }
        }
    }

    public function secureToken()
    {
        if (Sing_Session::exists(SG_SESS_TOKEN_KEY)) {
            $token = Sing_Session::get(SG_SESS_TOKEN_KEY);
            echo '<input type="hidden" name="'. SG_TRANSACTION_TOKEN. '" value="'. $token. '" />'."\n";
        }
    }

    /**
     * ビュースクリプトで利用するログイン確認メソッド。
     *
     * @return boolean TRUE:ログインしている
     */
    public function isLogined()
    {
        return (Sing_Session::getAuthUser()) ? true : false;
    }

    /**
     * ユーザがエラー状態にしている、もしくは例外が発生しているか検査します。
     *
     * @return boolean TRUE:エラーもしくは例外
     */
    public function isError()
    {
        foreach ($this->notices as $notice) {
            list($level, $message) = $notice;
            switch ($level) {
                case SG_NOTICE_ERROR:
                    return true;
                default:
                    break;
            }
        }
        if (Sing_Const::FAILUR == Sing_Runner::getInstance()->getStatus()) {
            return true;
        }
        return false;
    }

    /* ------------------------------------------------------------------------------------------------------------- */

    private function _value($paramName)
    {
        // リクエストパラメータから取得
        $value = arrVal($this->params, $paramName);

        // データストアから取得
        if (empty($value)) {
            $storeParams = Sing_Session::store();
            if ($storeParams) {
                $value = arrVal($storeParams, $paramName);
            }
        }

        // 検索結果から取得
        if (empty($value)) {
            $searchConds = $this->searchConds;
            if ($searchConds) {
                $value = arrVal($searchConds, $paramName);
            }
        }

        return $value;
    }
}
