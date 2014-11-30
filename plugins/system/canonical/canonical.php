<?php

/**
 * @version		$Id: canonical.php 3 2014-01-07 16:31:21Z grigormihov $ 
 * @package		Joomla/StyleWare 
 * @subpackage	Content * @copyright	Copyright (C) 2011 StyleWare.EU. All rights reserved. 
 * @license		GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgSystemCanonical extends JPlugin {

  function plgSystemCanonical(&$subject, $config) {
    parent::__construct($subject, $config);
  }

  function onBeforeCompileHead() {
    $mainframe = JFactory::getApplication();
    if ($mainframe->isAdmin()) {
      return;
    }
    $doc = JFactory::getDocument();
    // remove the shits set by Joomla!
    foreach ($doc->_links as $k => $array) {
      if ($array['relation'] == 'canonical') {
        unset($doc->_links[$k]);
      }
    }
  }

}