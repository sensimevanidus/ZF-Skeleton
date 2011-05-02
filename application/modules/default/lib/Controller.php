<?php

class ZFS_Default_Controller extends Zend_Controller_Action {
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
        $this->_initLocale();

        // register the logger object
        $this->_logger = Zend_Registry::get('logger');
    }

    protected function _initLocale() {
        $this->view->lang = $this->getRequest()->getParam('lang', 'en');
        $this->lang = $this->view->lang;
        $locale = new Zend_Locale($this->view->lang);

        $module = 'default';
        $path = APPLICATION_PATH . '/modules/default/language/' .  $this->lang . '.mo';
        $this->translate = new Zend_Translate('gettext', $path, $this->lang);

        if ($this->translate->isAvailable($locale)) {
            $this->translate->setLocale($locale);
        }

        $this->view->translate = $this->translate;
        Zend_Registry::set('translate', $this->translate);
    }
}