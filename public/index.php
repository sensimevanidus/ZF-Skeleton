<?php

// define path to application library
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH',
        realpath(dirname(__FILE__) . '/../application'));

// define application environment (in case it's not declared in .htaccess or
// can't be reached although declared)
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
        (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

// Ensure that library and models paths are on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/models'),
    get_include_path(),
)));

// Zend Application
require_once 'Zend/Application.php';

// create application, bootstrap and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()->run();