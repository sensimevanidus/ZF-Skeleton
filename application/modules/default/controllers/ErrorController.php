<?php

class ErrorController extends ZFS_Default_Controller
{
    public function errorAction() {
        // change the default layout
        $this->getHelper('Layout')->setLayout('layout');

        // get the error handler
        $errorHandler = $this->_getParam('error_handler');
        // set error messages
        switch ($errorHandler->type) {
            // 404 errors -- controller or action not found
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()
                     ->setRawHeader('HTTP/1.1 404 Not Found');
                break;

            // application error
            default:
                break;
        }

        // set view variables
        $this->view->pageTitle = 'Error';
        $this->view->exception = $errorHandler->exception;
        $this->view->request = $errorHandler->request;
    }
}
