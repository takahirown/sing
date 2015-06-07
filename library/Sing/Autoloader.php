<?php

class Sing_Autoloader extends Zend_Application_Module_Autoloader
{
    public function initDefaultResourceTypes()
    {
        $basePath = $this->getBasePath();
        $this->addResourceTypes(
            array(
                'dbtable'    => array(
                    'namespace' => 'Model_DbTable',
                    'path'      => 'models/DbTable',
                ),
                'mappers'    => array(
                    'namespace' => 'Model_Mapper',
                    'path'      => 'models/mappers',
                ),
                'form'       => array(
                    'namespace' => 'Form',
                    'path'      => 'forms',
                ),
                'model'      => array(
                    'namespace' => 'Model',
                    'path'      => 'models',
                ),
                'plugin'     => array(
                    'namespace' => 'Plugin',
                    'path'      => 'plugins',
                ),
                'service'    => array(
                    'namespace' => 'Service',
                    'path'      => 'services',
                ),
                'viewhelper' => array(
                    'namespace' => 'View_Helper',
                    'path'      => 'views/helpers',
                ),
                'viewfilter' => array(
                    'namespace' => 'View_Filter',
                    'path'      => 'views/filters',
                ),
                'component'    => array(
                    'namespace' => 'Component',
                    'path'      => 'components',
                ),
            )
        );
        $this->setDefaultResourceType('model');
    }
}
