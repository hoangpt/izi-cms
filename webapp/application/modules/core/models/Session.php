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
 * @version 	$Id: Session.php 5477 2010-09-20 09:59:00Z hoangpt $
 * @since		2.0.5
 */

/**
 * Represents session data
 */
class Core_Models_Session extends Izi_Model_Entity 
{
	protected $_properties = array(
		'session_id' => null,	/** Session Id */
		'data' 		 => null,	/** Session data */
		'modified' 	 => null,	/** The last modified date of session */
		'lifetime' 	 => null,	/** The session lifetime in seconds */
	);	
}
