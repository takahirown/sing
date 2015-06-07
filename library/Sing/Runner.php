<?php
require_once 'Sing/Const.php';

/**
 * @category  Sing
 * @author    Takahiro
 * @since     2014/12/28
 */
class Sing_Runner
{
    protected static $_instance = null;

    private $_options;

    private $_status = Sing_Const::SUCCESS;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function setStatus($status)
    {
        $this->_status = $status;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function getContainer()
    {
        return Sing_Container::getInstance();
    }

    /**
     * コマンドラインで実行しているか検査します。
     *
     * @return boolean TRUE:CLI
     */
    public static function isCli()
    {
        return (php_sapi_name() == 'cli') ? true : false;
    }

    /**
     * XmlHttpRequest通信が検査します。
     *
     * @return boolean TRUE:XmlHttpRequest通信
     */
    public static function isXmlHttpRequest()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        }
        return false;
    }

    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    public function getOptions()
    {
        if (null == $this->_options) {
            throw new Sing_Exception('アプリケーション・オプション(application.ini)が未設定です');
        }
        return $this->_options;
    }

    /**
     * Singを実行します。各コンポーネントの初期設定を行い、利用可能状態にします。
     *
     * @param string $key              - ファイル名キー（拡張子を除いたファイル名）
     * @param string $lang             - システムメッセージの言語（デフォルト ja）
     * @return void
     */
    public function run($key, $lang = 'ja')
    {
        $front = Zend_Controller_Front::getInstance();

        $this->addPlugin($front);

        $options = $this->getOptions();
        if (self::isCli()) {
            $front->setRouter(new Sing_Controller_Router_Cli());
        } else {
            Sing_Session::init($options['app']);

            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            $viewRenderer->setView(new Sing_View());

            Zend_Controller_Action_HelperBroker::addHelper(new Sing_Controller_Helper_Validator());
        }

        $this->loadConfig($key);

        $this->setEncoding();

        $this->initLog();

        $this->initMessage($lang);
    }

    private function addPlugin(Zend_Controller_Front $front)
    {
        $front->unregisterPlugin('Zend_Controller_Plugin_ErrorHandler'); //デフォルトは無効
        $front->registerPlugin(new Sing_Controller_Plugin_ErrorHandler());
        $front->registerPlugin(new Sing_Controller_Plugin_DBTransaction());
    }

    private function loadConfig($key)
    {
        $options = $this->getOptions();
        Sing_Configure::init($options['app']);
        Sing_Configure::load($key);
    }

    private function setEncoding()
    {
        $options = $this->getOptions();
        $encoding = $options['app']['internal_encoding'];
        mb_internal_encoding($encoding);
        mb_regex_encoding($encoding);
    }

    private function initLog()
    {
        $options = $this->getOptions();
        Sing_Log::init(realpath($options['app']['log_path']), $options['app']['log_format'], $options['app']['log_level']);
    }

    private function initMessage($lang)
    {
        Sing_Message::init($lang);
    }
}