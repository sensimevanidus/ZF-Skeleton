<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
    protected function _initAutoload() {
        // initialize the autoloader resource
        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
            'basePath' => APPLICATION_PATH . '/modules',
            'namespace' => 'ZFS'
        ));

        // add necessary resource types that will be autoloaded
        $resourceLoader
            ->addResourceType('formDefault', 'default/forms/', 'Form_Default')
            ->addResourceType('libDefault', 'default/lib/', 'Default')
            ->addResourceType('formApi', 'api/forms/', 'Form_Api')
            ->addResourceType('libApi', 'api/lib/', 'Api')
            ->addResourceType('formCockpit', 'cockpit/forms/', 'Form_Cockpit')
            ->addResourceType('libCockpit', 'cockpit/lib/', 'Cockpit');
    }

    protected function _initLogging() {
        // create the logger object
        $logger = new Zend_Log();

        // fetch logging options from the configuration file
        $logOptions = $this->getOption('logging');

        // filter the log messages
        $logger->addFilter(
            new Zend_Log_Filter_Priority((int) $logOptions['level'])
        );

        // register each log writer to the logging object
        foreach ($logOptions['writer'] as $writer) {
            if ('Zend_Log_Writer_Stream' == $writer) {
                $logger->addWriter(new $writer($logOptions['writer']['stream']['path']));
            } else if (is_string($writer)) {
                $logger->addWriter(new $writer());
            }
        }

        // make the logger object available to other parts of the application
        // via registry container
        Zend_Registry::set('logger', $logger);
    }

    protected function _initModules() {
        $front = Zend_Controller_Front::getInstance();
        $front->addControllerDirectory(APPLICATION_PATH . '/modules/default/controllers', 'default');
        $front->addControllerDirectory(APPLICATION_PATH . '/modules/api/controllers', 'api');
        $front->addControllerDirectory(APPLICATION_PATH . '/modules/cockpit/controllers', 'cockpit');
    }

    protected function _initPlugins() {
        // load the acl plugin
        $loader = new Zend_Loader_PluginLoader();
        $loader->addPrefixPath('ZFS_Default_Controller_Plugin', APPLICATION_PATH . '/modules/default/controllers/plugins');
        $loader->addPrefixPath('ZFS_Api_Controller_Plugin', APPLICATION_PATH . '/modules/api/controllers/plugins');
        $loader->addPrefixPath('ZFS_Cockpit_Controller_Plugin', APPLICATION_PATH . '/modules/cockpit/controllers/plugins');
    }

    protected function _initHelpers() {
        // ensure that custom helpers are found
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . '/modules/default/controllers/helpers',
            'ZFS_Default_Controller_Helper'
        );
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . '/modules/api/controllers/helpers',
            'ZFS_Api_Controller_Helper'
        );
        Zend_Controller_Action_HelperBroker::addPath(
            APPLICATION_PATH . '/modules/cockpit/controllers/helpers',
            'ZFS_Cockpit_Controller_Helper'
        );
    }

    protected function _initLayouts() {
        // Ensure the front controller is initialized
        $this->bootstrap('FrontController');

        // Retrieve the front controller from the bootstrap registry
        $front = $this->getResource('FrontController');

        // setup the layout
        // Workarond: the initial layout is the default module
        Zend_Layout::startMvc(array('layoutPath' => APPLICATION_PATH . '/modules/default/layouts'));

        // make sure that the ZF-Library is found on include path
        require_once 'SE/Controller/Plugin/Layout.php';
        $layoutModulePlugin = new SE_Controller_Plugin_Layout();
        $layoutModulePlugin->registerModuleLayout(
            'cockpit', APPLICATION_PATH . '/modules/cockpit/layouts'
        );
        $front->registerPlugin($layoutModulePlugin);

        $request = new Zend_Controller_Request_Http();

        $front->setRequest($request);

        // Ensure the request is stored in the bootstrap registry
        return $request;
    }

    protected function _initRoutes() {
        // Create route with language id (lang)
        $routeLang = new Zend_Controller_Router_Route(
            ':lang',
            array(
                'lang' => 'en'
            ),
            array('lang' => '[a-z]{2}')
        );

        // Now get router from front controller
        $front  = $this->getResource('frontcontroller');
        $router = $front->getRouter();

        $fallbackRoute = new Zend_Controller_Router_Route(':controller/:action/*',
            array('module' => 'default', 'controller' => 'index', 'action' => 'index')
        );

        $restRoute = new Zend_Rest_Route($front);
        $restRouteDefault = $routeLang->chain($restRoute);

        // Instantiate default module route
        $routeDefault = new Zend_Controller_Router_Route_Module(
            array(),
            $front->getDispatcher(),
            $front->getRequest()
        );

        // Chain it with language route
        $routeLangDefault = $routeLang->chain($routeDefault);

        $router->addRoute('defaultWeb', $fallbackRoute);
        
        // Add both language route chained with default route and
        // plain language route
        $router->addRoute('default', $routeLangDefault);
        $router->addRoute('lang', $routeLang);
        $router->addRoute('api', $restRouteDefault);

        // add the cockpit module
        $router->addRoute(
            'cockpitModule',
            new Zend_Controller_Router_Route('cockpit/:controller/:action',
                array('module' => 'cockpit', 'controller' => 'index', 'action' => 'index')
            )
        );
    }
    
    /**
     * @TODO Update the Zend Framework and remove this method
     * A workaround for the problem occured during unit-testing (could not use
     * Zend_Controller_Action->getInvokeArg('bootstrap'))
     * details can be found here: http://framework.zend.com/issues/browse/ZF-8193
     */
    protected function _initBootstrap() {
        if (null === Zend_Controller_Front::getInstance()->getParam('bootstrap')) {
            Zend_Controller_Front::getInstance()->setParam('bootstrap', $this);
        }
    }

}