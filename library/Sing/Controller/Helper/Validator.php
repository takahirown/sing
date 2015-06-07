<?php

/**
 * @category  Sing_Controller_Helper
 * @author    t.watanabe
 * @since     2015/05/10
 */
class Sing_Controller_Helper_Validator extends Zend_Controller_Action_Helper_Abstract
{
    public $configType = Sing_Const::TYPE_JSON;

    public function validate()
    {
        $aName = $this->getRequest()->getActionName();
        $controller = $this->getActionController();

        $rules = $this->parseConfig();

        try {
            $validator = Sing_Validator::create($controller->params(), $rules[$aName]);
            if (!$validator->isValid()) {
                $controller->setErrors($validator->getMessages());
                return false;
            }
        } catch (Exception $e) {
            Sing_Log::error($e->getMessage());
            throw new Sing_Controller_Helper_Exception('バリデート実施中に例外発生', 0, $e);
        }
        return true;
    }

    private function parseConfig()
    {
        $mName = $this->getRequest()->getModuleName();
        if ($mName === 'default') {
            $mName = '';
        } else {
            $mName .= DS;
        }
        $cName = $this->getRequest()->getControllerName();
        $aName = $this->getRequest()->getActionName();

        $rulePath = Sing_Configure::read('validator_rule_path');

        if (Sing_Const::TYPE_JSON === $this->configType) {

            $ruleConfig = $rulePath . DS . $mName. $cName. '.json';
            if (!file_exists($ruleConfig)) {
                throw new Sing_Controller_Helper_Exception('バリデーション定義が見つかりません： '. $ruleConfig);
            }
            $json = file_get_contents($ruleConfig);
            $rules = json_decode($json, true);

        } else {

            $ruleConfig = $rulePath . DS . $mName. $cName. '.php';
            if (!file_exists($ruleConfig)) {
                throw new Sing_Controller_Helper_Exception('バリデーション定義が見つかりません： '. $ruleConfig);
            }
            $rules = require_once($ruleConfig);

        }

        if (!isset($rules[$aName])) {
            throw new Sing_Controller_Helper_Exception('バリデーション定義が見つかりません： validation/'. $mName. $cName. '.php@'. $aName);
        }

        return $rules;
    }
}