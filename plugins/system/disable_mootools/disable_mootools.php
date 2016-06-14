<?php

/**
 * @version		$Id: disable_mootools.php $
 * @package		Joomla Extensions
 * @copyright	Copyright (C) 2006 - 2011 Union D. All rights reserved.
 * @license		GNU/GPL v2 or later
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemDisable_mootools extends JPlugin {

  function onBeforeCompileHead() {

    $app = JFactory::getApplication();

    if ($app->isAdmin()) {
      //	params support
      //	@since in v.1.0.1
      $on = $this->params->get('enabled', 0);
      if ($on) {
        $doc = JFactory::getDocument();
        $uri = JUri::getInstance();

        //	looking for scripts
        $headers = $doc->getHeadData();
        $scripts = isset($headers['scripts']) ? $headers['scripts'] : array();
        //	cleare the original scripts
        $headers['scripts'] = array();
        $headers['script'] = array();
        //	deleting mootols...

        foreach ($scripts as $url => $type) {

          $relativePath = trim(str_replace($uri->getPath(), '', JUri::root()), '/');
          $relativeScript = trim(str_replace($uri->getPath(), '', $url), '/');
          $relativeUrl = str_replace($relativePath, '', $url);

          if (strpos($url, 'html5fallback') === false)
					{
						$headers['scripts'][$url] = $type;
					}
          // Try to disable relative and full URLs
          //unset($doc->_scripts[$url]);
          //unset($doc->_scripts[$relativeUrl]);
          //unset($doc->_scripts[JUri::root(true) . $url]);
          //unset($doc->_scripts[$relativeScript]);
        }



        //	set the new head data
        $doc->setHeadData($headers);
        //JHTML::_('behavior.preventmootools');
        //JHTML::_('behavior.mootools');
      }
    }
  }

}
