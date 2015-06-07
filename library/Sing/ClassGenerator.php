<?php

/**
 * @category  Sing
 * @author    t.watanabe
 * @since     2015/03/21
 */
class Sing_ClassGenerator
{
    public static function create($className, $options = null)
    {
        if (!class_exists($className)) {
            throw new Sing_Exception('クラスファイルが見つかりません： '. $className);
        }
        $class = new ReflectionClass($className);
        if ($class->implementsInterface('Zend_Validate_Interface')) {
            if (($class->hasMethod('__construct')) && ($options != null)) {
                $instance = $class->newInstance($options);
            } else {
                $instance = $class->newInstance();
            }
        } else {
            $instance = $class->newInstance();
        }
        return $instance;
    }
}
