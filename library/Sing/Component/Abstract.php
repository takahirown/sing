<?php

/**
 * @category  Sing_Component
 * @author    t.watanabe
 * @since     2015/05/16
 */
abstract class Sing_Component_Abstract implements Sing_Component
{
    public function getName()
    {
        $fullClassName = get_class($this);
        if (strpos($fullClassName, '_') !== false) {
            $helperName = strrchr($fullClassName, '_');
            return ltrim($helperName, '_');
        } elseif (strpos($fullClassName, '\\') !== false) {
            $helperName = strrchr($fullClassName, '\\');
            return ltrim($helperName, '\\');
        } else {
            return $fullClassName;
        }
    }
}