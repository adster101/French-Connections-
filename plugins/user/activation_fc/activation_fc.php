<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('joomla.utilities.date');

/**
 * An example custom profile plugin.
 *
 * @package		Joomla.Plugin
 * @subpackage	User.profile
 * @version		1.6
 */
class plgUserActivation_fc extends JPlugin {

  function onUserAfterSave($data, $isNew, $result, $error) {

    $app = JFactory::getApplication();
    
    // Get the inputs so we can see whether we need to process anything or not
    $input = $app->input;
    $task = $input->get('task', '', 'string');
    $view = $input->get('view', '', 'string');
    $option = $input->get('option', '', 'string');
    $layout = $input->get('layout', '', 'string');
    $advertiser = $input->get('advertiser', false, 'boolean');

    // User is activating
    if ($task == 'activate' && $advertiser == true) {
      
      $url = $this->params->get('redirect','');
      $admin = JFactory::getApplication('administrator');
      
      $admin->redirect($url, 'Thank you. Your account has been successfully activated.');
      
    }
  
    return true;
  }

 
}
