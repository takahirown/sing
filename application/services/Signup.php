<?php

class Service_Signup
{
    /**
     * @var Model_Signup
     */
    private $model;

    public function registerAccount(array $params)
    {
        $this->model = Sing_ClassGenerator::create('Model_Signup');

        if (!$this->model->registerAccount($params)) {
            return false;
        }

        $mail = new Sing_Mail(Sing_Configure::read('mail_template_path'));
        $mail->setFromAddress('t.watanabe@micro-wave.net')
            ->setFromName('mw')
            ->send($params['email'], 'mail.signup.complete.ja.txt', array('email' => $params['email']));

        return true;
    }

    /**
     * @param string $value       - パラメータ値
     * @param array $params       - すべてのリクエストパラメータ
     * @return boolean TRUE:チェックが正常
     */
    public static function checkAlreadyRegistered($value, $params)
    {
        $model = new Model_Signup();
        if ($model->findByEmail($value)) {
            return false;
        }
        return true;
    }
}