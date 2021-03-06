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
 * @version 	$Id: TranslatablePage.php 4810 2010-08-24 03:36:37Z hoangpt $
 * @since		2.0.8
 */

class Page_View_Helper_TranslatablePage
{
	const EOL = "\n";
	
	/**
	 * Display select box listing all pages
	 * which haven't been translated from the default language
	 * 
	 * @param $attributes array
	 * @param string $lang
	 * @return string
	 */
	public function translatablePage($attributes = array(), $lang = null)
	{
		$defaultLang     = Izi_Config::getConfig()->localization->languages->default;
		$elementDisabled = ($lang != null && $lang == $defaultLang) ? ' disabled="disabled"' : '';
		if (null == $lang) {
			$lang = $defaultLang;
		}
		
		$conn = Izi_Db_Connection::factory()->getMasterConnection();
		$pageDao = Izi_Model_Dao_Factory::getInstance()->setModule('page')->getPageDao();
		$pageDao->setDbConnection($conn);
		$pages = $pageDao->getTranslatable($lang);
		
		$output = sprintf("<select name='%s' id='%s' viewHelperClass='%s' viewHelperAttributes='%s'%s>", 
							$attributes['name'], $attributes['id'], get_class($this), Zend_Json::encode($attributes), $elementDisabled) . self::EOL
				. '<option value=\'{"id": "", "language": ""}\'>---</option>' . self::EOL;
		
		foreach ($pages as $page) {
			$disabled = (0 == (int)$page->translatable 
							&& ((0 == (int)$attributes['disabled'] && $page->page_id != (int)$attributes['selected'])
								||
							((int)$attributes['disabled'] == (int)$attributes['selected'])))
						? ' disabled="disabled"' : '';
			$selected = ($elementDisabled == ''
							&& $disabled == ''
							&& (int)$page->page_id == (int)$attributes['selected'])
						? ' selected="selected"' : '';
			
			$output  .= sprintf("<option value='%s'%s%s>%s</option>", 
								Zend_Json::encode(array('id' => $page->page_id, 'language' => $defaultLang)), 
								$selected,
								$disabled,
								str_repeat('---', $page->depth) . $page->name) . self::EOL;
		}
		$output .= '</select>' . self::EOL;

		return $output;
	}	
}
