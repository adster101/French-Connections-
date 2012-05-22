<?php
/**
* @version		$Id: disable_mootools.php $
* @package		Joomla Extensions
* @copyright	Copyright (C) 2006 - 2011 Union D. All rights reserved.
* @license		GNU/GPL v2 or later
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

class  plgSystemDisable_mootools extends JPlugin {

	function onAfterDispatch() {
		
		$app	= JFactory::getApplication();
		
		//	for admin we must to load MooTools anyway...
		if (!$app->isAdmin()) {
			//	params support
			//	@since in v.1.0.1
			$on = $this->params->get('enabled', 0);
			if ($on) {
				$doc = JFactory::getDocument();
				//	looking for scripts
				$headers = $doc->getHeadData();
				$scripts = isset($headers['scripts']) ? $headers['scripts'] : array();
				//	cleare the original scripts
				$headers['scripts'] = array();
				//	deleting mootols...
				
				foreach($scripts as $url=>$type) 
				{
					if (strpos($url, 'mootools') === false && strpos($url, 'js/caption.js') === false) 
					{
						$headers['scripts'][$url] = $type;
					}
				}
				
				//	set the new head data
				$doc->setHeadData($headers);
				//JHTML::_('behavior.preventmootools');
				//JHTML::_('behavior.mootools');
			}
		}
	}	

}