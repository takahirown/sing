<?php

/**
 * @author t.watanabe
 * @since  2015/05/17
 */
class Component_Authorize extends Sing_Component_Abstract
{
    public function execute()
    {
        header('location: /login/');
        exit;
    }
}