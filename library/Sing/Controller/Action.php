<?php

/**
 * Zend_Controller_Action の拡張基底クラス
 *
 * @category  Sing_Controller
 * @author    t.watanabe
 * @since     2014/07/26
 */
class Sing_Controller_Action extends Zend_Controller_Action
{
    /**
     * View object
     * @var Sing_View
     */
    public $view;

    /**
     * エラー情報
     * @var array メッセージキーとメッセージ本文からなる連想配列
     */
    private $_errors = array();

    /**
     * 通知情報
     * @var array 通知レベルとメッセージ本文からなる連想配列を保持する配列
     */
    private $_notices = array();

    /**
     * ビュースクリプトをレンダリングするかどうか
     * @var boolean デフォルトTRUE
     */
    protected $viewRender = true;

    /* ------------------------------------------------------------------------------------------------------------- */

    /**
     * @see Zend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {
        if (property_exists($this, 'service')) {
            $className = 'Service_'. $this->getClassName();
            $this->service = Sing_ClassGenerator::create($className);
        }

        if (property_exists($this, 'model')) {
            $className = 'Model_'. $this->getClassName();
            $this->model = Sing_ClassGenerator::create($className);
        }

        if (Zend_Controller_Action_HelperBroker::hasHelper('AuthManager')) {
            $this->_helper->AuthManager->execute();
        }

        // 初回アクセス（POSTリクエストでない）の場合、データストアをクリア
        if (!$this->getRequest()->isPost()) {
            Sing_Session::clearStore();
        }
    }

    /**
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch()
    {
        $this->saveSecureToken();

        $this->view->params      = $this->getAllParams();
        $this->view->errors      = $this->_errors;
        $this->view->notices     = $this->_notices;
        $this->view->searchConds = $this->searchCondition();

        if (!$this->viewRender) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
        }
    }

    /* ------------------------------------------------------------------------------------------------------------- */

    /**
     * リクエストパラメータを取得。$paramName がセットされていなければリクエストパラメータのすべてが返されます。
     * パラメータが存在しないならば NULL を返します。
     *
     * パラメータが存在しない、且つ $default がセットされている場合 NULL の代わりに $default を返します。
     *
     * $readSession が TRUE の時（デフォルト）はセッション上の値も参照します。
     * 優先順位は リクエスト ＞ セッションになります。
     *
     * @param string $paramName        - パラメータ名
     * @param string $default          - デフォルト値
     * @param boolean $readSession     - TRUE:セッション（データストア）も見る | FALSE:リクエストパラメータのみ見る
     * @return mixed
     */
    public function params($paramName = null, $default = null, $readSession = true)
    {
        $params = $this->getAllParams();
        if ($readSession) {
            $storeData = Sing_Session::store();
            if ($storeData) {
                $params = array_merge($storeData, $params);
            }
        }
        if ($paramName === null) {
            return $params;
        }
        return (isset($params[$paramName])) ? $params[$paramName] : $default;
    }

    /**
     * 連想配列でビューに変数をアサインします。既にアサイン済みの場合は上書きになります。
     *
     * @param array $variables
     */
    protected function assign(array $variables)
    {
        if (empty($variables)) {
            return;
        }
        foreach ($variables as $key => $val) {
            $this->view->$key = $val;
        }
    }

    /**
     * ビュースクリプト名（拡張子を含まない）を指定してビュースクリプトを変更します。
     * 同一コントローラのビュースクルプのみセット可能です。
     *
     * @param string $scriptName
     * @return void
     */
    protected function changeScript($scriptName)
    {
        $this->_helper->viewRenderer($scriptName);
    }

    /* ------------------------------------------------------------------------------------------------------------- */

    /**
     * 入力値のデータバリデーションを行います。
     *
     * @return boolean                 - TRUE:エラー無し
     */
    protected function validate()
    {
        if (Zend_Controller_Action_HelperBroker::hasHelper('Validator')) {
            return $this->_helper->Validator->validate();
        }
    }

    protected function isGet($name = null, $value = null)
    {
        if ($this->getRequest()->isGet()) {
            return $this->checkParam($name, $value);
        }
        return false;
    }

    protected function isPost($name = null, $value = null)
    {
        if ($this->getRequest()->isPost()) {
            return $this->checkParam($name, $value);
        }
        return false;
    }

    /**
     * トランザクショントークンを含むセキュアなPOSTリクエストかどうか検査します。
     * 検査する場合は、ビュースクリプト内でトークンを出力してください（ $this->secureToken() ）。
     *
     * $name、$value がセットされている場合は、セキュア判定の他にパラメータの値判定も行います。
     * 複数ボタンがあるような画面でどのボタンが押下されているか判定する場合などで利用できます。
     *
     * @throws Sing_Controller_Exception
     * @param string $name             - ボタンの name 属性値
     * @param string $value            - ボタンの value 属性値
     * @return boolean セキュアなPOSTリクエストの場合は TRUE を返します
     */
    protected function isSecurePost($name = null, $value = null)
    {
        if ($this->getRequest()->isPost()) {
            if (!$this->isTokenValid()) {
                throw new Sing_Controller_Exception('トランザクショントークンが一致しません');
            }
            return $this->checkParam($name, $value);
        }
        return false;
    }

    private function checkParam($name, $value)
    {
        //Sing_Session::remove(SG_SESS_TOKEN_KEY);

        if ($name && $value) {
            if ($this->params($name) == $value) {
                return true;
            }
            return false;
        }
        if ($name && is_null($value)) {
            if ($this->params($name)) {
                return true;
            }
            return false;
        }
        return true;
    }

    private function saveSecureToken()
    {
        Sing_Session::set(SG_SESS_TOKEN_KEY, Sing_Security::uniqString());
    }

    /**
     * ユーザの現在のセッションに保持しているトークンと、 リクエストパラメータとして送信されるトークンを比較し、マッチしていなければ FALSE を
     * 返します。CSRF(クロスサイトリクエストフォージェリ)防止でデータ更新の際に利用してください。
     *
     * @return boolean                 - TRUE:正常
     */
    private function isTokenValid()
    {
        $sessionToken = Sing_Session::get(SG_SESS_TOKEN_KEY);
        $paramToken = $this->params(SG_TRANSACTION_TOKEN, null, false);
        return ($paramToken && $sessionToken == $paramToken) ? true : false;
    }

    /**
     * エラーメッセージのKey:Message配列をまとめてセットします。内部で Sing_Controller_Action::addError() をコールしています。
     *
     * @param array $errors
     * @see Sing_Controller_Action::addError()
     */
    public function setErrors(array $errors)
    {
        foreach ($errors as $key => $message) {
            $this->addError($key, $message);
        }
    }

    /**
     * エラー内容をセットします。既に同一のキーでエラー内容が追加されている場合は何もしません。
     *
     * @param string $key              - メッセージキー
     * @param string $message          - メッセージ本文
     * @return void
     */
    protected function addError($key, $message)
    {
        if (isset($this->_errors[$key])) {
            return;
        }
        $this->_errors[$key] = $message;
    }

    /**
     * 通知レベルを指定して画面通知するメッセージをセットします。追加した順に画面出力します。
     *
     * @param int $level               - 通知レベル SG_NOTICE_SUCCESS | SG_NOTICE_WARNING | SG_NOTICE_ERROR
     * @param string $message          - メッセージ本文
     * @return void
     */
    protected function addNotice($level, $message)
    {
        $this->_notices[] = array($level, $message);
    }

    /* ------------------------------------------------------------------------------------------------------------- */

    /**
     * 検索条件を保持、及び取得します。引数が空でコールした場合は保持している検索条件を取得し、引数を指定してコールした場合は検索条件を保持します。
     *
     * @param array $data              - 検索条件を含むパラメータデータ
     * @param array $conditionNames    - パラメータデータの中の対象パラメータ
     * @return mixed
     */
    protected function searchCondition(array $data = null, array $conditionNames = null)
    {
        $module     = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $action     = $this->getRequest()->getActionName();

        $sessionKey = 'sing.search.cond.'. $module. $controller. $action;

        if ($data === null && $conditionNames === null) {
            return Sing_Session::get($sessionKey);
        }

        $condMap = array();
        foreach ($conditionNames as $conditionName) {
            $condMap[$conditionName] = arrVal($data, $conditionName);
        }

        Sing_Session::set($sessionKey, $condMap);
    }

    /**
     * 保持している検索条件をクリア
     *
     * @return void
     */
    protected function clearSearchCondition()
    {
        $module     = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        $action     = $this->getRequest()->getActionName();

        $sessionKey = 'sing.search.cond.'. $module. $controller. $action;

        if (Sing_Session::exists($sessionKey)) {
            Sing_Session::remove($sessionKey);
        }
    }

    /* ------------------------------------------------------------------------------------------------------------- */

    /**
     * モジュール名、コントローラ名からクラス名を取得します。
     *
     * @return string
     */
    private function getClassName()
    {
        $mName = ucwords($this->getRequest()->getModuleName());
        if ($mName === 'Default') {
            $mName = '';
        }
        $cName = ucwords($this->getRequest()->getControllerName());
        return $mName. $cName;
    }
}