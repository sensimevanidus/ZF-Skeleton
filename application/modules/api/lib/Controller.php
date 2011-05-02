<?php

class ZFS_Api_Controller extends Zend_Rest_Controller {
    /**
     * @var Zend_Translate
     */
    protected $translate;
    protected $lang = 'en';
    
    /**
     * @var Zend_Log
     */
    protected $_logger;

    public function init() {
        //$this->_disableViewAndLayout();
        $this->_helper->layout()->disableLayout();
        
        $this->_initLocale();

        // register the logger object
        $this->_logger = Zend_Registry::get('logger');

        // the context
        $contextSwitch = $this->_helper->getHelper('contextSwitch');
        $contextSwitch->addActionContext('index', array('json'))
                      ->addActionContext('get', array('json'))
                      ->addActionContext('post', array('json'))
                      ->addActionContext('put', array('json'))
                      ->addActionContext('delete', array('json'))
                      ->initContext();
    }

    /**
     * The index action handles index/list requests; it should respond with a
     * list of the requested resources.
     */
    public function indexAction() {
      $this->getResponse()
            ->setHttpResponseCode(405);
      $this->view->error = $this->_setErrorMesssage("405 Method Not Allowed", 405);
    }

    /**
     * The get action handles GET requests and receives an 'id' parameter; it
     * should respond with the server resource state of the resource identified
     * by the 'id' value.
     */
    public function getAction() {
      $this->getResponse()
            ->setHttpResponseCode(405);
      $this->view->error = $this->_setErrorMesssage("405 Method Not Allowed", 405);
      
    }

    /**
     * The post action handles POST requests; it should accept and digest a
     * POSTed resource representation and persist the resource state.
     */
    public function postAction() {
      $this->getResponse()
            ->setHttpResponseCode(405);
      $this->view->error = $this->_setErrorMesssage("405 POST Method Not Allowed", 405);
    }

    /**
     * The put action handles PUT requests and receives an 'id' parameter; it
     * should update the server resource state of the resource identified by
     * the 'id' value.
     */
    public function putAction() {
        $this->getResponse()
            ->setHttpResponseCode(405);
        $this->view->error = $this->_setErrorMesssage("405 Method Not Allowed", 405);
    }

    /**
     * The delete action handles DELETE requests and receives an 'id'
     * parameter; it should update the server resource state of the resource
     * identified by the 'id' value.
     */
    public function deleteAction() {
        $this->getResponse()
            ->setHttpResponseCode(405);
        $this->view->error = $this->_setErrorMesssage("405 Method Not Allowed", 405);
    }

    protected function _initLocale() {
        $locale = new Zend_Locale($this->lang);

        $path = APPLICATION_PATH . '/modules/api/language/' .  $this->lang . '.mo';
        $this->translate = new Zend_Translate('gettext', $path, $this->lang);
        if ($this->translate->isAvailable($locale)) {
            $this->translate->setLocale($locale);
        }

        Zend_Registry::set('translate', $this->translate);
    }

    protected function _setErrorMesssage($msg, $code = 500) {
        $error = new stdClass();
        $error->code = $code;
        $error->msg = $msg;
        return $error;
    }
    
}