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
 * @version 	$Id: Widget.php 3553 2010-07-11 07:55:41Z hoangpt $
 * @since		2.0.7
 */

class Core_Widgets_DashboardVersion_Widget extends Izi_Widget_Dashboard 
{
	/**
	 * URL to get the latest version number of izicms
	 * 
	 * @const string
	 */
	const CHECK_VERSION_API = 'http://api.izicms.com/get_version.php';	
	
	protected function _prepareShow()
	{
		/**
		 * Get the latest version info
		 * Inform if there is newer version
		 * @since 2.0.1
		 */
		$hasNewerVersion = false;
		$latestVersion 	 = null;
		try {
			$latestVersion = Izi_Utility_HttpRequest::getResponse(self::CHECK_VERSION_API);
			if (version_compare(Izi_Version::getVersion(), $latestVersion, '<')) {
				$hasNewerVersion = true;
			}
		} catch (Exception $ex) {
		}
		
		/**
		 * Get general information
		 */
		$this->_view->assign('hasNewerVersion', $hasNewerVersion);
		$this->_view->assign('currentVersion', Izi_Version::getVersion());
		$this->_view->assign('latestVersion', $latestVersion);	
	}
}
