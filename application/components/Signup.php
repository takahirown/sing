<?php

class Component_SignupValitator
{
    public static function doCheck($value, $params)
    {
        //Zend_Debug::dump($value);
        //Zend_Debug::dump($params);

        $model = new Model_ExampleEx02();
        if ($model->selectAll()) {
            return true;
        }
        return false;
    }
}