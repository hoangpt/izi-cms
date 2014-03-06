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
 * @version 	$Id: Hook.php 3352 2010-06-28 06:16:48Z hoangpt $
 * @since		2.0.5
 */

interface Core_Models_Interface_Hook
{
	/**
	 * Get list of hooks
	 * 
	 * @return Izi_Model_RecordSet
	 */
	public function getHooks();
	
	/**
	 * Get the list of modules which has at least one hook
	 * 
	 * @return Izi_Model_RecordSet
	 */
	public function getModules();
	
	/**
	 * Add new hook
	 * 
	 * @param Core_Models_Hook $hook
	 * @return int
	 */
	public function add($hook);

	/**
	 * Check wheter a hook exists or not
	 * 
	 * @param Core_Models_Hook $hook
	 * @return boolean
	 */
	public function exist($hook);
	
	/**
	 * Delete hook by given Id
	 * 
	 * @param int $id Id of hook
	 * @return int
	 */
	public function delete($id);
	
	/**
	 * Install a hook
	 * 
	 * @param Core_Models_Hook $hook
	 * @return int
	 */
	public function install($hook);
	
	/**
	 * Uninstall a hook
	 * 
	 * @param Core_Models_Hook $hook
	 */
	public function uninstall($hook);
}
