<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of HelloWorld component
 */
class RentalController extends JControllerLegacy {

  /**
   * Checks whether a user can see this view.
   *
   * @param   string	$view	The view name.
   *
   * @return  boolean
   * @since   1.6
   */
  protected function canView($view) {
    $canDo = RentalHelper::getActions();

    switch ($view) {
      // Special permissions.
      case 'notes':
        return $canDo->get('helloworld.view.' . $view);
        break;

      // Default permissions.
      default:
        return true;
    }
  }

  /**
   * display task
   *
   * @return void
   */
  function display($cachable = false) {

    $view = $this->input->get('view', '');
    $layout = $this->input->get('layout', 'default');
    $id = $this->input->getInt('id');
    $unit_id = $this->input->getInt('unit_id');
    
    if (!$this->canView($view)) {
      JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
      return false;
    }

    // Set up an array of views to protect from direct access
    $views_to_protect = array('listing', 'tariffs', 'offers', 'unitversions', 'availability');

    // set default view if not set
    JRequest::setVar('view', JRequest::getCmd('view', 'Listings'));

    // Set the default view name and format from the Request.
    $vName = JRequest::getCmd('view', 'Property');

    // A check in each sub-controller is also needed to ensure that the user does actually own the item id
    if (in_array($vName, $views_to_protect) && !$this->checkEditId('com_rental.edit.' . $vName, $id)) {
      // Somehow the person just went to the form - we don't allow that.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(JRoute::_('index.php?option=com_rental&view=listings', false));

      return false;
    }

    if (($vName == 'images') && !$this->checkEditId('com_rental.edit.unitversions', $unit_id)) {
      // Somehow the person just went to the form - we don't allow that.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $unit_id));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(JRoute::_('index.php?option=com_rental&view=listings', false));

      return false;
    }

    if ($vName == 'reviews' && !$this->checkEditId('com_rental.view.unitversions', $id)) {
      // Somehow the person just went to the form - we don't allow that.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(JRoute::_('index.php?option=com_rental&view=listings', false));

      return false;
    }

    if ($vName == 'stats' && !$this->checkEditId('com_rental.stats.view', $id)) {
      // Somehow the person just went to the form - we don't allow that.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(JRoute::_('index.php?option=com_rental&view=listings', false));

      return false;
    }

    // call parent behavior
    parent::display($cachable);
  }

  function changeLanguage() {
    $id = JRequest::getInt('id');
    $session = & JFactory::getSession();
    $session->set('com_rental.property.' . $id . '.lang', JRequest::getVar('Language'));
    $view = JRequest::getVar('view');
    $this->setRedirect('index.php?option=com_rental&task=' . $view . '.edit&id=' . $id);
  }

}
