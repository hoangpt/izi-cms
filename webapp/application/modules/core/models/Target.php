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
 * @version 	$Id: Target.php 5478 2010-09-20 09:59:05Z hoangpt $
 * @since		2.0.0
 */

/**
 * Represents a hook target
 */
class Core_Models_Target extends Izi_Model_Entity
{
	protected $_properties = array(
		'target_id' 	=> null,	/** Id of target */
		'target_module' => null,	/** Name of target module */
		'target_name' 	=> null,	/** Name of target */
		'description' 	=> null,	/** Description of target */
		'hook_module' 	=> null,	/** Name of hook module */
		'hook_name' 	=> null,	/** Name of hook */
		'hook_type' 	=> null,	/** Type of hook. Can be action or filter */
	);
}
