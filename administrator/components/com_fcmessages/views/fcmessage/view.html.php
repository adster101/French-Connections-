<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * HTML View class for the Messages component
 *
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 * @since       1.6
 */
class FcMessagesViewFcMessage extends JViewLegacy {

  protected $form;
  protected $item;
  protected $state;

  public function display($tpl = null) {
    $this->form = $this->get('Form');
    $this->item = $this->get('Item');
    $this->state = $this->get('State');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode("\n", $errors));
      return false;
    }

    parent::display($tpl);
    $this->addToolbar();
  }

  /**
   * Add the page title and toolbar.
   *
   * @since   1.6
   */
  protected function addToolbar() {

    JToolbarHelper::title(JText::_('COM_MESSAGES_VIEW_PRIVATE_MESSAGE'), 'envelope inbox');
    

    JToolbarHelper::cancel('fcmessage.cancel', 'Close');
    JToolbarHelper::help('config', true);
  }

}