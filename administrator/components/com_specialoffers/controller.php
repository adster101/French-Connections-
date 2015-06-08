<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * General Controller of HelloWorld component
 */
class SpecialOffersController extends JControllerLegacy
{

  /**
   * display task
   *
   * @return void
   */
  public function display($cachable = false, $urlparams = array())
  {
    // set default view if not set
    JRequest::setVar('view', JRequest::getCmd('view', 'SpecialOffers'));

    $view = $this->input->get('view', 'specialoffers');
    $layout = $this->input->get('layout', 'default');
    $id = $this->input->getInt('id');

    // Check for edit form.
    if ($view == 'specialoffer' && $layout == 'edit' && !$this->checkEditId('com_specialoffers.edit.specialoffer', $id))
    {
      // Somehow the person just went to the form - we don't allow that.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(JRoute::_('index.php?option=com_specialoffers', false));

      return false;
    }
    parent::display($cachable, $urlparams);
  }

}
