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
 * @version 	$Id: RequestLog.php 5031 2010-08-28 17:26:40Z hoangpt $
 * @since		2.0.5
 */

class Core_Models_Dao_Sqlsrv_RequestLog extends Izi_Model_Dao
	implements Core_Models_Interface_RequestLog
{
	public function convert($entity) 
	{
		return new Core_Models_RequestLog($entity); 
	}
	
	public function create($log) 
	{
		$this->_conn->insert($this->_prefix . 'core_request_log', array(
			'ip'		  => $log->ip,
			'agent'		  => $log->agent,
			'browser'	  => $log->browser,
			'version'	  => $log->version,
			'platform'	  => $log->platform,
			'bot'		  => $log->bot,
			'uri'		  => $log->uri,
			'full_url'	  => $log->full_url,
			'refer_url'	  => $log->refer_url,
			'access_time' => $log->access_time,
		));
	}
}
