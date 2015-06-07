<?php

class LoginController extends Sing_Controller_Action
{
    public function indexAction()
    {
        $this->assignVariable();

        if ($this->getRequest()->isPost()) {

            if (SG_VALID_ERROR == $this->validate($this->params())) {
                return;
            }

            $submit = $this->params('submit');
            if ('login' == $submit) {

                $model = new Model_Account();

                // 条件を満たしていればログイン認証失敗回数を初期化
                $model->clearLoginFailures($this->params('email'));

                // IDとパスワードで認証
                if ($account = $model->auth($this->params('email'), $this->params('password'))) {

                    // セッションにアカウントを登録
                    Sing_Session::setAuth($account, ($this->params('rememberMe')) ?
                        Sing_Configure::read('session_remember_me_seconds') : null);

                    // ログイン後の自動遷移処理
                    if (strpos($this->getRequest()->getServer('HTTP_REFERER'), 'callbackUrl') !== false) {
                        $referer = urldecode($this->getRequest()->getServer('HTTP_REFERER'));
                        $tempStr = substr($referer, strpos($referer, '?callbackUrl')+13);
                        list($callbackUrl, $uniqKey) = explode('&key=', $tempStr);
                        if ($uniqKey == Sing_Session::get('callbackRedirectKey') &&
                                strpos($callbackUrl, $_SERVER['HTTP_HOST'])) {
                            Sing_Session::remove('callbackRedirectKey');
                            return $this->redirect($callbackUrl);
                        }
                        throw new Sing_Exception('ExceptionLoginCallbackKey');
                    }

                    return $this->redirect('/mypage/');
                }

                // ログイン認証失敗回数を加算、加算結果に合わせたメッセージを取得
                $errorMessage = $model->countupLoginFailures($this->params('email'));
                $this->addNotice(SG_NOTICE_ERROR, $errorMessage);

            }
        }
    }

    public function logoutAction()
    {
        Sing_Session::destroyAuth();
        $this->redirect('/');
    }

    /* ------------------------------------------------------------------------------------------------------------- */

    /**
     * @see Sing_Controller::assignVariable()
     */
    protected function assignVariable($params = null)
    {
        $this->view->title = 'Login';

        $this->view->options = array(
                'rememberMe' => array(
                        '1'  => 'ログイン状態を記録する<br />Remember me',
                ),
        );
    }

//     /**
//      * @see Sing_Controller::validate()
//      */
//     protected function validate(array $params)
//     {
//         // ID
//         if(!Zend_Validate::is($params['email'], 'NotEmpty')){
//             $this->addError('email', Sing_Message::get('NotEmpty', 'ID'));
//         }

//         // パスワード
//         if(!Zend_Validate::is($params['password'], 'NotEmpty')){
//             $this->addError('password', Sing_Message::get('NotEmpty', 'パスワード'));
//         }

//         return $this->hasError();
//     }
}