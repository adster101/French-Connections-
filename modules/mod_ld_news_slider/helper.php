<?php
/*
 * Helper file for LD News Slider
 * @package Joomla!
 * @Copyright (C) 2010 littledonkey.net
 * @ All rights reserved
 * @ Joomla! is Free Software
 * @ Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 1.2.0 $
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
// import com_content route helper
require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');
// import JString class for UTF-8 problems
jimport('joomla.utilities.string'); 
// Main class
class ModLDnewsSliderHelper 
{
	/*
	 * Declare global class variables
	*/
	
	var $items;
	var $head;
	var $args = array();
	
	function getArticles(&$params) {
		

		$db = &JFactory::getDBO();
		$nullDate = $db->getNullDate();
		$date =& JFactory::getDate();
    	$now = $date->toMySQL();


      

      
		$query  = "select cn.id, ca.id as catid, ca.alias as catalias, cn.alias as conalias, cn.images as art_images, cn.sectionid,cn.introtext, ";
	 	$query .= "CASE WHEN CHAR_LENGTH(cn.alias) THEN CONCAT_WS(':', cn.id, cn.alias) ELSE cn.id END as slug, ";
	 	$query .= "CASE WHEN CHAR_LENGTH(ca.alias) THEN CONCAT_WS(':', ca.id, ca.alias) ELSE ca.id END as catslug, ";
     	$query .= "if (length(cn.title)>999,concat(substring(cn.title,1,999),'...'),cn.title) as title, ";
     	$query .= "cn.title as fulltitle ";
     	$query .= "from #__content as cn , #__categories as ca ";
     	$query .= "where cn.id <> '' ";
     	
     	if (count($params->get('articles')) > 0) { 
			$query .= " and cn.id in (" . ($params->get('articles')) .")";
		} else {
			$query .= " and cn.catid = " . $params->get('category');		
		}
     	
      	$query .= " and state = 1 and ca.id=cn.catid ";

   		$query .= ' and ( publish_up = '.$db->Quote($nullDate).' or publish_up <= '.$db->Quote($now).' )';
    	$query .= ' and ( publish_down = '.$db->Quote($nullDate).' or publish_down >= '.$db->Quote($now).' )';
      
    	//if ($order_field == "random"){
    	 	//$query .= " order by RAND() ";
    	//}else{
    		//$query .= " order by ".$order_field." ".$order_by;
    	//}
      
    	//if ($no_of_items != 0) {
    		//$query .= " limit ".$no_of_items;
    	//}
    	$db->setQuery($query);
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
		
		$document->addStyleSheet( 'modules/mod_ld_news_slider/styles/slider.css', 'text/css' );		
		$document->addScript( 'modules/mod_ld_news_slider/scripts/jquery.orbit-1.4.0.js' );
		require(JModuleHelper::getLayoutPath('mod_ld_news_slider', 'default'));
		
		
	}
	
}
