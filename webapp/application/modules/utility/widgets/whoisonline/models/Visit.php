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
 * @version 	$Id: Visit.php 5394 2010-09-12 16:05:40Z hoangpt $
 * @since		2.0.9
 */

class Utility_Widgets_WhoIsOnline_Models_Visit extends Izi_Model_Entity
{
	protected $_properties = array(
		'visit_id'     => null,
		'ip' 		   => null,
		'access_time'  => null,
		'country' 	   => null,
		'country_code' => null,
		'user_id' 	   => null,
		'user_name'    => null,
	);
}