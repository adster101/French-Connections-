<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of HelloWorld component
 */
class RentalController extends JControllerLegacy
{

  /**
   * Checks whether a user can see this view.
   *
   * @param   string	$view	The view name.
   *
   * @return  boolean
   * @since   1.6
   */
  protected function canView($view, $option)
  {

    $user = JFactory::getUser();
    $asset = $this->name . '.' . $view . '.view';

    if ($user->authorise($asset, $option))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * display task
   *
   * @return void
   */
  function display($cachable = false)
  {

    // Set the default view for this component
    JRequest::setVar('view', JRequest::getCmd('view', 'listings'));

    // Get the GET params for this view
    $view = $this->input->get('view', 'listings');
    $option = $this->input->getCmd('option', 'com_rental');
    $property_id = ($this->input->getInt('id')) ? $this->input->getInt('id') : $this->input->getInt('property_id');
    $unit_id = $this->input->getInt('unit_id');
    $context = $option . '.edit.' . $view;

    // Basic check to ensure user is allowed to access this view.
    if (!$this->canView($view, $option))
    {
      $this->setRedirect('index.php');
      return false;
    }

    // Check whether the user has accessed this item correctly already...
    // Test all the relevant views and that the 'edit ids' are held in the session
    if (!$this->checkEditId($context, $property_id) || !$this->checkEditId($context, $unit_id) && $view !=='review')
    {
      // Somehow the person just went to the form - we don't allow that.
      //$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', ''));
      //$this->setMessage($this->getError(), 'error');
      $this->setRedirect(JRoute::_('index.php', false));
      return false;
    }

    // call parent behavior
    parent::display($cachable);
  }

  function changeLanguage()
  {
    $id = JRequest::getInt('id');
    $session = & JFactory::getSession();
    $session->set('com_rental.property.' . $id . '.lang', JRequest::getVar('Language'));
    $view = JRequest::getVar('view');
    $this->setRedirect('index.php?option=com_rental&task=' . $view . '.edit&id=' . $id);
  }

}
