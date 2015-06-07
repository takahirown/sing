<?php

class Example_Ex07Controller extends Sing_Controller_Action
{
    public function indexAction()
    {
        $this->view->title = 'Ex07.PHPUnit画面実行サンプル';
    }

    public function executeAction()
    {
        if (!chdir(APPLICATION_PATH . '/../tests')) {
            throw new Sing_Exception('ディレクトリの移動に失敗しました。 path:' . APPLICATION_PATH . '/../tests');
        }

        $stdout = null;
        $command = 'php phpunit.phar --bootstrap library/bootstrap.php library';
        exec($command, $stdout);

        $message = '';
        $addSpanFlg = false;

        foreach ($stdout as $line) {
            if ('FAILURES!' == $line) {
                $message .= '<span style="color:red">';
                $addSpanFlg = true;
            } elseif (Sing_String::exist($line, 'OK (')) {
                $message .= '<span style="color:green">';
                $addSpanFlg = true;
            }
            $message .= $line . '<br />';
        }

        if ($addSpanFlg) {
            $message .= '</span>';
        }

        $this->view->message = $message;
    }
}