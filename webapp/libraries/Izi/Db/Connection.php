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
 * @version 	$Id: Connection.php 3986 2010-07-25 16:32:46Z hoangpt $
 * @since		2.0.0
 */

class Izi_Db_Connection
{
	/**
	 * @return Izi_Db_Connection_Abstract
	 */
	public static function factory()
	{
		$config  = Izi_Config::getConfig();
		$adapter = $config->db->adapter;
        $adapter = str_replace(' ', '_', ucwords(str_replace('_', ' ', strtolower($adapter))));
		$class 	 = 'Izi_Db_Connection_' . $adapter . '_Connection';
		if (!class_exists($class)) {
			throw new Exception('Does not support ' . $adapter . ' connection');
		}
		$instance = new $class($adapter);
		return $instance;
	}
}
