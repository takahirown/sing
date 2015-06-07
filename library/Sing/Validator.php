<?php

/**
 * データバリデーションを行うクラス
 *
 * バリデーションのルールをJSONで記述することで少ないコード量で誰が書いても一定の品質が出ることを目的としています。
 *
 * 検証ロジックはZend_Validateを利用します。
 * ※現時点では開発中でZend_Validateのすべての動作を確認していません。
 *
 * @category  Sing
 * @author    t.watanabe
 * @since     2015/03/14
 */
class Sing_Validator
{
    /**
     * 検証対象情報
     * @var array
     */
    private $_params;

    /**
     * バリデーションルール情報
     * @var array
     */
    private $_rules;

    /**
     * エラーメッセージ情報
     * @var array
     */
    private $_messages;

    /**
     * バリデーションクラスのインスタンスを生成します。
     *
     * @param array $params            - 検証対象情報
     * @param array $rules             - バリデーションルール、検証対象毎にパイプ区入で複数設定可能
     * @return Sing_Validator
     */
    public static function create(array $params, array $rules)
    {
        return new static($params, $rules);
    }

    /**
     * 隠蔽されたコンストラクタ。
     *
     * @param array $params            - 検証対象情報
     * @param array $rules             - バリデーションルール
     */
    private function __construct(array $params, array $rules)
    {
        $this->_messages = array();
        $this->_params = $params;
        $this->_rules = $this->divideRules($rules);
    }

    /**
     * 指定されたバリデーションルールを処理可能な状態まで分解し返却します。
     *
     * @param array $rules             - バリデーションルール
     * @return array
     */
    private function divideRules(array $rules)
    {
        $result = array();
        foreach ($rules as $paramName => $validatorArray) {
            $work = array();
            $work['name'] = $paramName;

            list($paramDisplayName, $validatorString) = $validatorArray;
            $work['displayName'] = $paramDisplayName;

            $validatorList = explode('|', $validatorString);

            $validators = array();
            foreach ($validatorList as $validatorText) {
                $validators[] = $this->buildClassContexts($validatorText, $paramDisplayName);
            }
            $work['validators'] = $validators;

            $result[] = $work;
        }

        return $result;
    }

    /**
     * 指定したルールで検証対象をバリデーションします。
     *
     * @return boolean TRUE:エラーなし
     */
    public function isValid()
    {
        $isSuccess = true;
        foreach ($this->_rules as $paramRule) {
            $paramName = $paramRule['name'];

            // 必須チェックでない、且つ値が空の場合は検証しない
            $isRequired = $this->existNotEmpty($paramRule['validators']);
            if (!$isRequired && (!isset($this->_params[$paramName]) || empty($this->_params[$paramName]))) {
                continue;
            }

            foreach ($paramRule['validators'] as $validatorContext) {
                if ($validator = $this->constructValidator($validatorContext)) {
                    // バリデーション実施
                    if (!$validator->isValid($this->_params[$paramName])) {
                        $isSuccess = false;

                        // エラーメッセージを詰める、ひとつの検証対象で詰められるエラーメッセージは最初に詰めたひとつのみ
                        if (!isset($this->_messages[$paramName])) {
                            //var_dump($validatorContext['messages']);
                            $this->_messages[$paramName] =
                                Sing_Message::get($validatorContext['key'], $validatorContext['messages']);
                        }
                    }
                }
            }
        }
        return $isSuccess;
    }

    /**
     * 必須チェックのルールが存在しているか。
     *
     * @param array $validators        - 検証項目のバリデーション情報
     * @return boolean TRUE:必須チェック有り
     */
    private function existNotEmpty(array $validators)
    {
        foreach ($validators as $validatorContext) {
            if ($validatorContext['key'] === 'NotEmpty') {
                return true;
            }
        }
        return false;
    }

    /**
     * エラーメッセージを取得します。
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * バリデータの構築コンテキストからインスタンスを生成します。
     *
     * @param array $validatorContext
     * @return Zend_Validate_Interface
     */
    private function constructValidator(array $validatorContext)
    {
        $className = $validatorContext['className'];
        $options = $validatorContext['options'];
        return Sing_ClassGenerator::create($className, $options);
    }

    /**
     * バリデーションルール文字列からバリデータの構築コンテキストを組み立てます。
     *
     * @param string $validatorText
     * @param string $paramDisplayName
     * @return array バリデータの構築コンテキスト
     */
    private function buildClassContexts($validatorText, $paramDisplayName)
    {
        if (strpos($validatorText, '::') !== false) {
            /* 独自バリデータ */

            if (strpos($validatorText, '[') === false) {//var_dump('-----------');
                list($className, $methodName) = explode('::', $validatorText);
                return array(
                    'key'       => $validatorText,
                    'messages'  => array($paramDisplayName),
                    'className' => 'Zend_Validate_Callback',
                    'options'   => array('callback' => array($className, $methodName), 'options' => array($this->_params)),
                );
            } else {//var_dump('-----------');
                $arrWork = explode('[', $validatorText);
                list($className, $methodName) = explode('::', $arrWork[0]);
                return array(
                    'key'       => $arrWork[0],
                    'messages'  => $this->getErrorMesages($arrWork[1], $paramDisplayName),
                    'className' => 'Zend_Validate_Callback',
                    'options'   => array('callback' => array($className, $methodName), 'options' => array($this->_params)),
                );
            }

        } else {
            /* Zendバリデータ */

            if (strpos($validatorText, '[') === false) {//var_dump('-----------');
                return array(
                    'key'       => $validatorText,
                    'messages'  => array($paramDisplayName),
                    'className' => 'Zend_Validate_'. $validatorText,
                    'options'   => null,
                );
            } else {//var_dump('-----------');
                $arrWork = explode('[', $validatorText);//var_dump($arrWork);
                return array(
                    'key'       => $arrWork[0],
                    'messages'  => $this->getErrorMesages($arrWork[1], $paramDisplayName),
                    'className' => 'Zend_Validate_'. $arrWork[0],
                    'options'   => Sing_String::convertArray(substr($arrWork[1], 0, -1)),
                );
            }
        }
    }

    /**
     * 表示パラメータ、[]で指定している表示メッセージを配列にセットして取得します。
     *
     * @param string $optionString
     * @param string $paramDisplayName
     * @return array
     */
    private function getErrorMesages($optionString, $paramDisplayName)
    {
        $messages = Sing_String::convertArray(substr($optionString, 0, -1));//var_dump($messages);
        array_unshift($messages, $paramDisplayName);//var_dump($messages);
        return $messages;
    }
}