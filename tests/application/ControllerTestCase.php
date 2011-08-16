<?php
require_once 'Zend/Application.php';
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

abstract class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase {
    protected $application;

    public function setUp() {
        $this->bootstrap = array($this, 'appBootstrap');
        parent::setUp();
    }

    public function assertRedirect($message = ''){
        $message += "\n" .$this->getResponse()->getBody();
        parent::assertRedirect($message);
    }

    public function assertController($controller, $message = ''){
        $message += "\n" . $this->getResponse()->getBody();
        parent::assertController($controller, $message);
    }
}
