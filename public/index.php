<?php

defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ?
    getenv('APPLICATION_ENV') : 'development'));

defined('DOCUMENT_ROOT') || define('DOCUMENT_ROOT', dirname(__FILE__));

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(DOCUMENT_ROOT . '/../application'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

require_once 'Zend/Application.php';
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

if (isset($argv)) {
    Zend_Registry::set('argv', $argv);
}

$application->bootstrap()->run();