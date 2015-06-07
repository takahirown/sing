<?php

/**
 * @category  Sing_Controller_Shell
 * @author    t.watanabe
 * @since     2015/03/22
 */
abstract class Sing_Controller_Shell_Action extends Sing_Controller_Action
{
    public function indexAction()
    {
        if (!Sing_Runner::isCli()) {
            throw new Sing_Controller_Shell_Exception('シェルをURL指定で実行しています');
        }

        try {
            $this->viewRender = false;
            $this->execute(explode(',', $this->params('args')));
        } catch (Exception $e) {
            Sing_Log::error('シェル実行で例外が発生しています');
            throw $e;
        }
    }

    abstract public function execute($args);
}