<?php

/**
 * 認証チェック用コマンド
 *
 * @author t.watanabe
 * @since  2015/05/09
 */
class Service_AuthCommand implements Sing_Command
{
    public function execute()
    {
        header('location: /login/');
        exit;
    }
}