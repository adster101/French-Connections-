<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

/**
 * User notes list view
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       2.5
 */
class RentalViewMarketing extends JViewLegacy
{

  /**
   * A list of user note objects.
   *
   * @var    array
   * @since  2.5
   */
  protected $items;

  /**
   * The model state.
   *
   * @var    JObject
   * @since  2.5
   */
  protected $state;

  /**
   * The model state.
   *
   * @var    JUser
   * @since  2.5
   */
  protected $user;

  /**
   * Override the display method for the view.
   *
   * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
   *
   * @return  mixed  A string if successful, otherwise a JError object.
   *
   * @since   2.5
   */
  public function display($tpl = null)
  {

    // Get the model state
    $this->state = $this->get('State');
    // Initialise view variables.
    $this->item = $this->get('Item');
    // Get the model form
    $this->form = $this->get('Form');

    $this->id = JFactory::getApplication()->input->get('property_id', '', 'int');

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      throw new Exception(implode("\n", $errors), 500);
    }

    $this->addToolbar();
    $this->sidebar = JHtmlSidebar::render();
    parent::display($tpl);
  }

  /**
   * Display the toolbar.
   *
   * @return  void
   *
   * @since   2.5
   */
  protected function addToolbar()
  {
    // Get the component permissions
    $canDo = RentalHelper::getActions();

    // Set the title on the title bar
    JToolbarHelper::title(JText::sprintf('COM_RENTAL_MARKETING_TITLE', $this->id), 'wand');
    JToolBarHelper::back();

    if ($canDo->get('core.create'))
    {
      $bar = JToolbar::getInstance('actions');

      $bar->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'marketing.apply', false);
      $bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'marketing.save', false);
    }

    // Get a toolbar instance so we can append the preview button
    $bar = JToolBar::getInstance('toolbar');
    $bar->appendButton('Preview', 'preview', 'COM_RENTAL_PROPERTY_PREVIEW', 'index.php');
  }

}
