<?php 

error_reporting(E_ALL);

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

// Define application environment
define('APPLICATION_ENV', 'testing');

// ensure that Doctrine's library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/models'),
    get_include_path(),
)));

/** Zend Application */
require_once 'Zend/Application.php';

require_once 'ControllerTestCase.php';

// create application, bootstrap and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->getBootstrap()->bootstrap();

$cli = new Doctrine_Cli( $application->getOption('doctrine'));
$cli->run(array("doctrine", "build-all-reload", "force"));


