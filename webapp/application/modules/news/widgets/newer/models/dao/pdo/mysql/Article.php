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
 * @version 	$Id: Article.php 4895 2010-08-24 20:20:43Z hoangpt $
 * @since		2.0.5
 */

class News_Widgets_Newer_Models_Dao_Pdo_Mysql_Article extends Izi_Model_Dao
	implements News_Widgets_Newer_Models_Interface_Article
{
	public function convert($entity)
	{
		return new News_Models_Article($entity);
	}
	
	public function getNewerArticles($limit, $article = null, $categoryId = null)
	{
		$select = $this->_conn
						->select()
						->from(array('a' => $this->_prefix . 'news_article'), array('article_id', 'category_id', 'title', 'slug', 'activate_date', 'image_square', 'icons'))
						/**
						 * @since 2.0.8
						 */
						->where('a.language = ?', $this->_lang);
		if ($categoryId) {
			$select->where('a.category_id = ?', $categoryId);
		}
		if ($article != null && $article->activate_date != null) {
			$select->where('a.activate_date > ?', $article->activate_date);
		}
		$select->where('a.status = ?', 'active')
				->order('a.activate_date DESC')
				->limit($limit);
		$rs = $select->query()->fetchAll();
		return new Izi_Model_RecordSet($rs, $this);		
	}
}
