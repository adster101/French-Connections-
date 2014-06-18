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
class RentalViewAdmin extends JViewLegacy
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

    $canDo = RentalHelper::getActions();

    JToolBarHelper::title(JText::_('COM_RENTAL_MANAGER_HELLOWORLDS'), 'helloworld');

    if ($canDo->get('core.edit'))
    {
      JToolBarHelper::save('save', '', false);
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

      $this->setModel(JModelLegacy::getInstance('Property', 'RentalModel'), true);

      // Initialise view variables.
      $this->form = $this->get('AdminForm');

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

  