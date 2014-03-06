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
 * @version 	$Id: WidgetController.php 5352 2010-09-09 03:21:56Z hoangpt $
 * @since		2.0.0
 */

class Core_WidgetController extends Zend_Controller_Action 
{
	/* ========== Frontend actions ========================================== */
	
	/**
	 * Load widget by Ajax
	 * 
	 * @return void
	 */
	public function ajaxAction() 
	{
//		Zend_Registry::set(Izi_GlobalKey::LOG_REQUEST, false);
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if (!$request->isPost()) {
			$this->getResponse()->setBody(Zend_Json::encode(array('content' => 'RESULT_INVALID_REQUEST')));
			return;
		}
		
		$module  = ucfirst(strtolower($request->getPost('mod')));
		$name 	 = ucfirst(strtolower($request->getPost('name')));
		$params  = $request->getPost('params');
		$act 	 = $request->getPost('act', 'show');
		
		if ($params != null) {
			$params = Zend_Json::decode($params);
		}
		$return = array(
			'css' 		 => array(), 
			'javascript' => array(), 
			'content' 	 => '',
		);
		if ($module == 'null' && $name == 'null') {
			/**
			 * Default output
			 */
			$this->getResponse()->setBody(Zend_Json::encode($return));
			return;
		}
		
		$res 	 = Izi_Widget_Resource::getResources(strtolower($module), strtolower($name));
		$search  = array('{APP_URL}', '{APP_STATIC_SERVER}');
		$replace = array($this->view->baseUrl(), $this->view->APP_STATIC_SERVER);
		foreach ($res['css'] as $style) {
			$return['css'][] = str_replace($search, $replace, $style);	
		}
		foreach ($res['javascript'] as $script) {
			$return['javascript'][] = array(
				'file' 	 => str_replace($search, $replace, $script),
				'script' => null,
			);
		}
		
		$widgetClass = $module . '_Widgets_' . $name . '_Widget';
		if (!class_exists($widgetClass)) {
			$this->getResponse()->setBody(Zend_Json::encode($return));
			return;
		}
		
		$timeout  = isset($params[Izi_Widget::PARAM_CACHE_LIFETIME]) ? $params[Izi_Widget::PARAM_CACHE_LIFETIME] : null;
		$cache 	  = Izi_Cache::getInstance();
		$cacheKey = $widgetClass . '_' . md5($module . '_' . $name . '_' . serialize($params));
		if ($cache && $timeout != null) {
			$cache->setLifetime($timeout);
		}
		
		$widget = new $widgetClass($module, $name);
		if (!($widget instanceof Izi_Widget)) {
			/**
			 * TODO: Throw exception
			 */
		} else {
			if ($act == 'show' && $cache && $timeout) {
				if (!($fromCache = $cache->load($cacheKey))) {
					$content = $widget->show($params);
					$cache->save($content, $cacheKey, array($module . '_Widgets'));
					$return['content'] = $content;
				} else {
					$return['content'] = $fromCache;
				}
			} else {
				$return['content'] = $widget->$act($params);
			}
			
			if ($this->view->headScript()->count() > 0) {
				$iterator = $this->view->headScript()->getIterator();
				foreach ($iterator as $item) {
					if ($item->source == null) {
						$return['javascript'][] = array(
							'file'	 => $item->attributes['src'],
							'script' => null,
						);
					} else {
						$return['javascript'][] = array(
							'file'	 => null,
							'script' => $item->source,
						);
					}
				}
			}
		}
		
		$this->getResponse()->setBody(Zend_Json::encode($return));		
	}
	
	/* ========== Backend actions =========================================== */
	
	/**
	 * Install widget
	 * 
	 * @return void
	 */
	public function installAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$module = $request->getPost('mod');
			$name 	= $request->getPost('name');
			
			$info = Izi_Widget_Config::getWidgetInfo($module, $name);
			if ($info) {
				$widget = new Core_Models_Widget($info);
				$conn = Izi_Db_Connection::factory()->getMasterConnection();
				$widgetDao = Izi_Model_Dao_Factory::getInstance()->setModule('core')->getWidgetDao();
				$widgetDao->setDbConnection($conn);
				$id = $widgetDao->add($widget);
				
				$this->getResponse()->setBody($module . ':' . $name . ':' . $id);
				
				/**
				 * Execute install queries in widget information file if there are
				 * @since 2.0.8
				 */
				$file = IZI_APP_DIR . DS . 'modules' . DS . $module . DS . 'widgets' . DS . $name . DS . 'about.xml';
				if (file_exists($file)) {
					$config  = Izi_Config::getConfig();
					$adapter = $config->db->adapter;
					
					$xml     = simplexml_load_file($file);
					$xpath 	 = $xml->xpath('install/db[contains(@adapter, "' . $adapter . '")]/query');
					if (is_array($xpath) && count($xpath) > 0) {
						foreach ($xpath as $query) {
							try {
								$query = str_replace('###', $config->db->prefix, (string)$query);
								Izi_Db_Connection::factory()->query($query);
							} catch (Exception $ex) {
								break;
							}
						}
					}
				}
			} else {
				$this->getResponse()->setBody('RESULT_ERROR');
			}
		}
	}
	
	/**
	 * List widgets
	 * 
	 * @return void
	 */
	public function listAction() 
	{
		$dir 	 = IZI_APP_DIR . DS . 'modules';
		$modules = Izi_Utility_File::getSubDir($dir);
		$widgets = array();
		foreach ($modules as $module) {
			/**
			 * Load all widgets from module
			 */
			$widgetDirs = Izi_Utility_File::getSubDir(IZI_APP_DIR . DS . 'modules' . DS . $module . DS . 'widgets');
			if (count($widgetDirs) > 0) {
				$widgets[$module] = array();
				foreach ($widgetDirs as $widgetName) {
					$info = Izi_Widget_Config::getWidgetInfo($module, $widgetName);
					if ($info != null) { 				
						$widgets[$module][] = new Core_Models_Widget($info);
					}
				}
			}
		}
		
		/**
		 * Get the list of widgets from database
		 */
		$dbWidgets = array();
		$conn = Izi_Db_Connection::factory()->getMasterConnection();
		$widgetDao = Izi_Model_Dao_Factory::getInstance()->setModule('core')->getWidgetDao();
		$widgetDao->setDbConnection($conn);
		$rs = $widgetDao->getWidgets();
		if ($rs) {
			foreach ($rs as $row) {
				$key = strtolower($row->module . ':' . $row->name); 
				$dbWidgets[$key] = $key . ':' . $row->widget_id;
			}
		}
		
		$modules = array_keys($widgets);
		
		$this->view->assign('dbWidgets', $dbWidgets);
		$this->view->assign('widgets', $widgets);
		$this->view->assign('modules', $modules);
	}
	
	/**
	 * Uninstall widget
	 * 
	 * @return void
	 */
	public function uninstallAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$module = $request->getPost('mod');
			$name 	= $request->getPost('name');
			$id 	= $request->getPost('id');
			
			$conn = Izi_Db_Connection::factory()->getMasterConnection();
			$widgetDao = Izi_Model_Dao_Factory::getInstance()->setModule('core')->getWidgetDao();
			$widgetDao->setDbConnection($conn);
			$widgetDao->delete($id);
			
			$this->getResponse()->setBody($module . ':' . $name);
			
			/**
			 * Execute uninstall queries in widget information file if there are
			 * @since 2.0.8
			 */
			$file = IZI_APP_DIR . DS . 'modules' . DS . $module . DS . 'widgets' . DS . $name . DS . 'about.xml';
			if (file_exists($file)) {
				$config  = Izi_Config::getConfig();
				$adapter = $config->db->adapter;
				
				$xml     = simplexml_load_file($file);
				$xpath 	 = $xml->xpath('uninstall/db[contains(@adapter, "' . $adapter . '")]/query');
				if (is_array($xpath) && count($xpath) > 0) {
					foreach ($xpath as $query) {
						try {
							$query = str_replace('###', $config->db->prefix, (string)$query);
							Izi_Db_Connection::factory()->query($query);
						} catch (Exception $ex) {
							break;
						}
					}
				}
			}
		}
	}
	
	/**
	 * Upload new widget
	 * 
	 * @return void
	 */
	public function uploadAction() 
	{
		$this->_helper->getHelper('viewRenderer')->setNoRender();
		$this->_helper->getHelper('layout')->disableLayout();
		
		$request = $this->getRequest();
		if ($request->isPost()) {
			$file 	 = $_FILES['file'];
			$prefix  = 'widget_' . time();
			$zipFile = IZI_TEMP_DIR . DS . $prefix . $file['name'];
			move_uploaded_file($file['tmp_name'], $zipFile);
			
			/**
			 * Process uploaded file
			 */
			$zip = Izi_Zip::factory($zipFile);
			$res = $zip->open();
			if ($res === true) {
				$tempDir = IZI_TEMP_DIR . DS . $prefix;
				if (!file_exists($tempDir)) {
					mkdir($tempDir);
				}
				$zip->extract($tempDir);
				
				/**
				 * Get the first (and only) sub-forder
				 */
				$subDirs = Izi_Utility_File::getSubDir($tempDir);
				$xml = $tempDir . DS . $subDirs[0] . DS . 'about.xml';
				
				$info = Izi_Widget_Config::getWidgetInfoFromXml($xml);
				if ($info) {
					/**
					 * TODO: Check whether the widget was already installed
					 */
					$widget = new Core_Models_Widget($info);
					$conn = Izi_Db_Connection::factory()->getMasterConnection();
					$widgetDao = Izi_Model_Dao_Factory::getInstance()->setModule('core')->getWidgetDao();
					$widgetDao->setDbConnection($conn);
					$id = $widgetDao->add($widget);
					
					/**
					 * Copy to the module directory
					 */
					$widgetDir = IZI_APP_DIR . DS . 'modules' . DS . $widget->module . DS . 'widgets' . DS . $widget->name;
					Izi_Utility_File::copyRescursiveDir($tempDir . DS . $subDirs[0], $widgetDir);
				} else {
					/**
					 * TODO: Still add the widget information to database without its about file
					 */
				}
				
				/**
				 * Remove all the temp files
				 */
				$zip->close();
				
				Izi_Utility_File::deleteRescursiveDir($tempDir);
				unlink($zipFile);
			}
			
			$this->_redirect($this->view->serverUrl() . $this->view->url(array(), 'core_widget_list'));
		}
	}
}
