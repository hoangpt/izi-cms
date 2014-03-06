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
 * @version 	$Id: Widget.php 3980 2010-07-25 15:59:24Z hoangpt $
 * @since		2.0.0
 */

class Utility_Widgets_Countdown_Widget extends Izi_Widget 
{
	protected function _prepareShow()
	{
		$event 	= $this->_request->getParam('event');
		$target = $this->_request->getParam('target');
		$target = getdate(strtotime($target));
		
		$this->_view->assign('event', $event);
		$this->_view->assign('target', $target);
	}
	
	protected function _prepareConfig()
	{
		$target = $this->_request->getParam('target');
		$this->_view->assign('target', $target);
	}
}
