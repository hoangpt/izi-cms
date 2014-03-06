<?php
/**
 * izicms
 * 
 * LICENSE
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE Version 2 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-2.0.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@izicms.com so we can send you a copy immediately.
 * 
 * @copyright	Copyright (c) 2009-2010 izicms Team (http://www.izicms.vn)
 * @license		http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE Version 2
 * @version 	$Id: Bootstrap.php 5497 2010-09-22 01:41:08Z hoangpt $
 * @since		2.0.3
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	/**
	 * Register auto loader
	 * 
	 * @return void
	 */
	protected function _initAutoload()
	{
		$autoloader = Zend_Loader_Autoloader::getInstance();
		/**
		 * FIXME: The following loader do not work
		 * <code>
		 * $namespaces = array('Hooks_', 'Plugins_');
		 * foreach ($modules as $module) {
		 * 		$namespaces[] = ucfirst($module) . '_';
		 * }
		 * $autoloader->unshiftAutoloader(new Izi_Autoloader(), $namespaces);
		 * </code>
		 */		
		
		$modules = Izi_Module_Loader::getInstance()->getModuleNames();
		new Izi_Autoloader(array(
    			'basePath'  => IZI_APP_DIR,
    			'namespace' => 'Hooks_',
			));
		new Izi_Autoloader(array(
    			'basePath'  => IZI_APP_DIR,
    			'namespace' => 'Plugins_',
			));
		foreach ($modules as $module) {
			new Izi_Autoloader(array(
    			'basePath'  => IZI_APP_DIR . DS . 'modules' . DS . $module,
    			'namespace' => ucfirst($module) . '_',
			));
		}

		require_once 'htmlpurifier/HTMLPurifier/Bootstrap.php';
		HTMLPurifier_Bootstrap::registerAutoload();
		
		return $autoloader;
	}

	/**
	 * Redirect to the install page if user have not installed yet
	 * 
	 * @since 2.0.3
	 * @return void
	 */
	protected function _initInstallChecker()
	{
		$config = Izi_Config::getConfig();
		if (null == $config->install || null == $config->install->date) {
			header('Location: install.php');
			exit;
		}
	}
	
	/**
	 * Init routes
	 * 
	 * @return void
	 */
	protected function _initRoutes()
	{
		$this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        
		$routes = Izi_Module_Loader::getInstance()->getRoutes();
		$front->setRouter($routes);
		
		/**
		 * Don't use default route
		 */
		$front->getRouter()->removeDefaultRoutes();
	}
	
	/**
	 * Init session
	 * 
	 * @return void
	 */
	protected function _initSession()
	{
		/** 
		 * Registry session handler 
		 */
		Zend_Session::setSaveHandler(Core_Services_SessionHandler::getInstance());
		
		/**
		 * Allow user to set more session settings in application.ini
		 * For example:
		 * session.cookie_lifetime = "3600"
		 * session.cookie_domain   = ".domain.ext"
		 * @since 2.0.9
		 */
		Zend_Session::setOptions(Izi_Config::getConfig()->web->session->toArray());
		
		if (isset($_GET['PHPSESSID'])) {
			session_id($_GET['PHPSESSID']);
		} else if (isset($_POST['PHPSESSID'])) {
			session_id($_POST['PHPSESSID']);
		}
	}
	
	/**
	 * Add action helpers
	 * 
	 * @since 2.0.7
	 * @return void
	 */
	protected function _initActionHelpers()
	{
		/**
		 * Protect forms/pages from CSRF attacks
		 */
		Zend_Controller_Action_HelperBroker::addHelper(new Izi_Controller_Action_Helper_Csrf());
		Zend_Controller_Action_HelperBroker::addPath(IZI_LIB_DIR . DS . 'Izi' . DS . 'Controller' . DS . 'Action' . DS . 'Helper',
													 'Izi_Controller_Action_Helper');
	}
	
	/**
	 * Register plugins
	 * 
	 * @return void
	 */
	protected function _initPlugins()
	{
		$this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        
		/** 
		 * Register plugins
		 * The alternative way is that put plugin to /application/config/application.ini:
		 * resources.frontController.plugins.pluginName = "Plugin_Class"
		 */
		$front->registerPlugin(new Core_Controllers_Plugin_Init())
				->registerPlugin(new Izi_Controller_Plugin_Admin())
		 		->registerPlugin(new Izi_Controller_Plugin_Template())
		 		->registerPlugin(new Core_Controllers_Plugin_HookLoader())
		 		->registerPlugin(new Core_Controllers_Plugin_Auth())
		 		
		 		/**
		 		 * @since 2.0.7
		 		 */
		 		->registerPlugin(new Core_Controllers_Plugin_Permalink())
		 		
		 		/**
		 		 * @since 2.0.8
		 		 */
		 		->registerPlugin(new Izi_Controller_Plugin_LocalizationRoute());
		 		
		/**
		 * Error handler
		 */
		$front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
								    'module' 	 => 'core',
								    'controller' => 'message',
								    'action'     => 'error',
								)));
		$conn = Izi_Db_Connection::factory()->getSlaveConnection();
		$pluginDao = Izi_Model_Dao_Factory::getInstance()->setModule('core')->getPluginDao();
		$pluginDao->setDbConnection($conn);
		$plugins = $pluginDao->getOrdered();
		
		foreach ($plugins as $plugin) {
			$pluginClass = 'Plugins_'.$plugin->name.'_Plugin';
			if (class_exists($pluginClass)) {
				$pluginInstance = new $pluginClass();
				if ($pluginInstance instanceof Izi_Controller_Plugin) {
					$front->registerPlugin($pluginInstance);
				}
			} else {
//				throw new Izi_Plugin_Exception('Plugin '.$plugin->name.' not found');
			}
		}
	}
}
