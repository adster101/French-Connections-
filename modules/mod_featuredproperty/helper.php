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
	 
		$db = JFactory::getDBO();
		$db->setQuery($db->getQuery(true)
			->select("id,params,greeting,description,lang,occupancy,swimming,latitude,longitude,nearest_town")
			->from("#__helloworld"),0,4
		);

   	$items = ($items = $db->loadObjectList())?$items:array();
   	$this->items = $items;
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
