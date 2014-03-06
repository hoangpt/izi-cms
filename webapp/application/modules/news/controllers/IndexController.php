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
 * @version 	$Id: IndexController.php 3975 2010-07-25 11:28:50Z hoangpt $
 * @since		2.0.0
 */

class News_IndexController extends Zend_Controller_Action 
{
	/* ========== Frontend actions ========================================== */
	
	public function indexAction() 
	{
		/**
		 * Add RSS link
		 */
		$this->view->headLink(array(
			'rel' 	=> 'alternate', 
			'type' 	=> 'application/rss+xml', 
			'title' => 'RSS',
			'href' 	=> $this->view->url(array(), 'news_rss_index'),
		));
	}
}
