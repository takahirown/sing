<?php

class Sing_Container
{
    private static $_instance = null;

    private $_components = null;

    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getComponents()
    {
        if (null === $this->_components) {
            $this->_components = new Zend_Registry();
        }
        return $this->_components;
    }

    public function addComponent(Sing_Component $component)
    {
        $resourceName = strtolower($component->getName());
        $this->getComponents()->{$resourceName} = $component;
        return $this;
    }

    public function hasComponent($name)
    {
        $resourceName = strtolower($name);
        return isset($this->getComponents()->{$resourceName});
    }

    public function getComponent($name)
    {
        $resourceName = strtolower($name);
        if ($this->hasComponent($resourceName)) {
            return $this->getComponents()->{$resourceName};
        }
        return null;
    }
}