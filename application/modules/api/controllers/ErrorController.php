<?php

class Api_ErrorController extends ZFS_Api_Controller {
    public function errorAction() {
        // get the error handler
        $errorHandler = $this->_getParam('error_handler');

        $logger = Zend_Registry::get('logger');
        $logger->err(
            $errorHandler->exception->getMessage() . "\n" .
            $errorHandler->exception->getTraceAsString()
        );

        // set error messages
        switch ($errorHandler->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
                $this->getResponse()
                     ->setRawHeader('HTTP/1.1 404 Not Found')
                     ->appendBody($this->translate->_("404 Not Found"));
                break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
                $this->getResponse()
                     ->setRawHeader('HTTP/1.1 405 METHOD NOT ALLOWED')
                     ->appendBody($this->translate->_("405 Method Not Allowed"));
                break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->getResponse()
                     ->setRawHeader('HTTP/1.1 400 BAD REQUEST')
                     ->appendBody($this->translate->_("400 Bad Request"));
                break;
            default:
                if($errorHandler->exception instanceof Sroups_RESTAuth_Exception) {
                  $this->getResponse()
                     ->setRawHeader('HTTP/1.1 401 Authentication Failed')
                     ->appendBody($this->translate->_("401 Authentication Failed"));
                } else {
                  $this->getResponse()
                     ->setRawHeader('HTTP/1.1 500 INTERNAL SERVER ERROR')
                     ->appendBody($this->translate->_("500 Internal Server Error"));
                }
        }
    }
}
