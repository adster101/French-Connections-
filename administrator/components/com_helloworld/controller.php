<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of HelloWorld component
 */
class HelloWorldController extends JControllerLegacy {

  /**
   * Checks whether a user can see this view.
   *
   * @param   string	$view	The view name.
   *
   * @return  boolean
   * @since   1.6
   */
  protected function canView($view) {
    $canDo = HelloWorldHelper::getActions();

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

    if (!$this->canView($view)) {
      JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
      return false;
    }

    // Set up an array of views to protect from direct access
    $views_to_protect = array('tariffs' => 1, 'offers' => 1, 'unitversions' => 1, 'availability ' => 1);

    // Get the document object.
    $document = JFactory::getDocument();

    // set default view if not set
    JRequest::setVar('view', JRequest::getCmd('view', 'Listings'));

    // Set the default view name and format from the Request.
    $vName = JRequest::getCmd('view', 'Property');
    $lName = JRequest::getCmd('layout', 'default');
    $id = JRequest::getInt('unit_id');

    // Check for edit form. This checks that the edit ID is set in the session.
    // This only occurs when someone follows a link ?option=com_helloworld&task=helloworld.edit&id=78
    // A check in each sub-controller is also needed to ensure that the user does actually own the item id
    if (array_key_exists($vName, $views_to_protect) && !$this->checkEditId('com_helloworld.edit.' . $vName, $id)) {
      // Somehow the person just went to the form - we don't allow that.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(JRoute::_('index.php?option=com_helloworld&view=listings', false));

      return false;
    }



    if (($vName == 'images') && !$this->checkEditId('com_helloworld.edit.unitversions', $id)) {
      // Somehow the person just went to the form - we don't allow that.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(JRoute::_('index.php?option=com_helloworld&view=listings', false));

      return false;
    }

    if ($vName == 'reviews' && !$this->checkEditId('com_helloworld.view.unitversions', $id)) {
      // Somehow the person just went to the form - we don't allow that.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(JRoute::_('index.php?option=com_helloworld&view=listings', false));

      return false;
    }

    if ($vName == 'stats' && !$this->checkEditId('com_helloworld.stats.view', $id)) {
      // Somehow the person just went to the form - we don't allow that.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(JRoute::_('index.php?option=com_helloworld&view=listings', false));

      return false;
    }

    // call parent behavior
    parent::display($cachable);
  }

  function changeLanguage() {
    $id = JRequest::getInt('id');
    $session = & JFactory::getSession();
    $session->set('com_helloworld.property.' . $id . '.lang', JRequest::getVar('Language'));
    $view = JRequest::getVar('view');
    $this->setRedirect('index.php?option=com_helloworld&task=' . $view . '.edit&id=' . $id);
  }

}
