<?php

class SignupController extends Sing_Controller_Action
{
    /**
     * @var Service_Signup
     */
    protected $service;

    public function indexAction()
    {
        $this->defaultAssign();


        // 確認処理
        if ($this->isSecurePost('submit', 'confirm')) {

            if (!$this->validate()) {
                $this->addNotice(SG_NOTICE_ERROR, '入力された値に誤りがあります。各エラー内容をご確認ください。');
                return;
            }

            Sing_Session::registerStore($this->params());
            return $this->changeScript('confirm');

        // 登録処理
        } elseif ($this->isSecurePost('submit', 'save')) {

            if (!$this->validate()) {
                $this->addNotice(SG_NOTICE_ERROR, '入力された値に誤りがあります。各エラー内容をご確認ください。');
                return;
            }

            if (!$this->service->registerAccount($this->params())) {
                $this->addNotice(SG_NOTICE_ERROR, 'アカウントの登録ができませんでした。');
                return;
            }

            Sing_Session::clearStore();
            return $this->changeScript('complete');

        // 戻る処理
        } elseif ($this->isSecurePost('submit', 'back')) {

            $this->redirect('/example/');

        // 修正処理
        } elseif ($this->isSecurePost('submit', 'edit')) {

        // 初期表示処理
        } else {

        }
    }

    private function defaultAssign()
    {
        $this->view->title = 'Ex04.会員登録フォーム・サンプル';
        $this->view->options = array(
                'gender' => array(
                        '0' => '女性',
                        '1' => '男性',
                ),
                'birthdayDayOfMonth'  => Sing_Array::between(1, 31),
                'birthdayMonthOfYear' => Sing_Array::between(1, 12),
                'birthdayYear'        => Sing_Array::between(1950, date('Y')),
        );
    }
}