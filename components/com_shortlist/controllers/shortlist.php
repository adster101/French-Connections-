<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 */
class ShortlistControllerShortlist extends JControllerLegacy {

  protected $isLoggedIn = false;

  public function isLoggedIn() {

    // Get the app object
    $app = JFactory::getApplication();

    // Get the input
    $input = $app->input;

    // property id
    $id = $input->get('id', '', 'int');

    // Action
    $action = $input->get('action', '', 'string');

    // Get the user object
    $user = JFactory::getUser();
    $user_id = $user->id;

    // Get the model
    $model = $this->getModel('ShortlistItem', 'ShortlistModel');

    // Check whether the user is logged in.
    if (!$user->guest) {
      $this->isLoggedIn = true;

      if (!$model->updateShortlist($user_id, $id, $action)) {
        // Update failed for whatever reason and has been logged.
      }
    } 
    
    $app->close();
    
    
    
    




    // Check model for errors
  }

}
