<?php 

/**
 * @package	Joomla.Tutorials
 * @subpackage	Module
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license	License GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die;

class modFeaturedPropertyHelper
{
	var $items;

  public function getFeaturedProperties() {
		$document =& JFactory::getDocument();
		$lang =& JFactory::getLanguage()->getTag();
		
		if ($lang === 'fr-FR') {
			$select = 'cat.title,catid,hel.id,hel.params,trans.greeting,trans.description,lang,occupancy,swimming,latitude,longitude,nearest_town';
		} else {
			$select = 'cat.title,catid,hel.id,hel.params,hel.greeting,hel.description,lang,occupancy,swimming,latitude,longitude,nearest_town';
		}

		$db = JFactory::getDBO();
		$db->setQuery($db->getQuery(true)
			->select($select)
			->from("#__helloworld as hel")    
      ->where('hel.parent_id !=0')

			->leftJoin('#__categories AS cat ON hel.catid = cat.id')
			->leftJoin('#__helloworld_translations AS trans ON hel.id = trans.property_id')
      ->order('rand()')
			,0,4
		);

		// Load the JSON string
		//$params = new JRegistry;
		//$params->loadJSON($this->item->params);
		//$this->item->params = $params;

   	$items = ($items = $db->loadObjectList())?$items:array();	
   	$this->items = $items;
		$this->lang = $lang;
	}

	function renderLayout(&$params) {
	
		// Do other stuff here to prepare content etc
		/**
			GENERATING FINAL XHTML CODE START
		**/
		// create instances of basic Joomla! classes
		$document =& JFactory::getDocument();
		$uri =& JURI::getInstance();
		// add stylesheets to document header
		
		
		require(JModuleHelper::getLayoutPath('mod_featuredproperty', 'default'));
		
		
	}
}
