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
class plgUserProfile_fc extends JPlugin {
  
  
  /*
   * Need to hijack the activation, change of password events for owner accounts
   */
  function onUserAfterSave($data, $isNew, $result, $error) {

    $userId = JArrayHelper::getValue($data, 'id', 0, 'int');

    $app = JFactory::getApplication();

    if (!$isNew && $app->isSite()) {

      if (in_array(10, $data['groups']) && count($data['groups'] == 1)) {

        $task = $app->input->get('task');

        $app->redirect('/administrator', 'Woot');
      }
    }



    // Need to hijack the email generation here as per the github plugin...





    return true;
  }

}
