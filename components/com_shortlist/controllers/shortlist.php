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

  protected $success = false;

  public function update() {

    // Check for request forgeries.
    if (!JSession::checkToken('GET')) {
      return $this->success;
    }

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
    if (!$user->guest) { // I.e. user is logged in (not a guest)
      if ($model->updateShortlist($user_id, $id, $action)) { // Explicity check on shortlist component permissions?
        // Updated okay
        $this->success = (int) 1;
      }
    }

    echo json_encode($this->success);

    $app->close();

    // Check model for errors
  }

  public function remove() {
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
    if (!$user->guest) { // I.e. user is logged in (not a guest)
      if ($model->updateShortlist($user_id, $id, $action)) { // Explicity check on shortlist component permissions?
        // Updated okay
        // Need to redirect back to the shortlist view, innit!
        $this->setMessage('Property removed from shortlist');
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=' . $this->option)
        );
      }
    }
  }

}
