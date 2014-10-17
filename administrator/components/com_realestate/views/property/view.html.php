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
class RealestateViewProperty extends JViewLegacy
{

  /**
   * The model form.
   *
   * @var    JUser
   * @since  2.5
   */
  protected $form;

  /**
   * Setting the toolbar
   */
  protected function addToolBar()
  {

    $canDo = RealEstateHelper::getActions();

    JToolBarHelper::title(JText::sprintf('COM_REALESTATE_MANAGER_MANAGE_PROPERTY', $this->id));

    JToolBarHelper::back();

    if ($canDo->get('core.edit'))
    {
      JToolBarHelper::save('property.save');
    }
  }

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

    // Set the id we are editing
    $this->id = JFactory::getApplication()->input->getInt('id', '');

    // Set the model
    $this->setModel(JModelLegacy::getInstance('Property', 'RealestateModel'), true);

    // Initialise view variables.
    $this->form = $this->get('Form');

    // Get the id we are updating
    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      throw new Exception(implode("\n", $errors), 500);
    }

    // Set the toolbar
    $this->addToolBar();

    parent::display($tpl);
  }

}

