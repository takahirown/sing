<?php

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
define('APPLICATION_ENV', 'testing');

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';
