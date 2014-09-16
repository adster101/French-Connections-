<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class RealEstateViewListing extends JViewLegacy
{

  protected $state;

  /**
   * Listing view display method
   * @return void
   */
  function display($tpl = null)
  {

    // Load the user profile landuage strings.
    $lang = JFactory::getLanguage();
    $lang->load('plg_user_profile_fc');

    // Get the model state
    $this->state = $this->get('State');

    // Add the submit model to this view so we can fetch the submit for approval form
    // And handle the associated logic...
    //$submit = $this->setModel(JModelLegacy::getInstance('Submit', 'RentalModel'));

    // Find the user details
    $user = JFactory::getUser();
    $userID = $user->id;

    // Get the ID
    $app = JFactory::getApplication();
    $this->id = $app->input->get('id', '', 'int');

    // Get data from the model
    $this->items = $this->get('Items');
    
    $model = $this->getModel();
    
    $this->status = $model->getProgress($this->items);

    $this->pagination = $this->get('Pagination');

    $this->state = $this->get('State');

    //$this->form = $submit->getForm($this->items);

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }

    // Set the toolbar
    $this->addToolBar();

    // Display the template
    parent::display($tpl);

    // Set the document
    $this->setDocument();
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar()
  {

    $document = JFactory::getDocument();

    $user = JFactory::getUser();

    $layout = JFactory::getApplication()->input->get('layout', 'default', 'string');

    $canDo = PropertyHelper::getActions();

    JToolBarHelper::title(count($this->items) > 0 ? JText::sprintf('COM_REALESTATE_LISTING_TITLE', $this->id) : 'No listings');

    // TO DO - For owners back should be to OA homepage, probably taken care of by permissions settings
    JToolBarHelper::back('COM_REALESTATE_BACK_TO_PROPERTY_LIST', '/administrator/index.php?option=com_realestate');
    //JToolbarHelper::help('', false, '/support');

    if ($layout == 'review')
    {
      if ($canDo->get('rental.listing.review'))
      {
        JToolBarHelper::deleteList('', 'listing.approve', 'BLAH');
      }
    }

    // Get a toolbar instance so we can append the preview button
    $bar = JToolBar::getInstance('toolbar');
    $property_id = $this->items[0]->id;
    $bar->appendButton('Preview', 'preview', 'COM_REALESTATE_PROPERTY_PREVIEW', $property_id);
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument()
  {
    $document = JFactory::getDocument();

    $document->setTitle(JText::_('COM_REALESTATE_ADMINISTRATION'));
    $document->addScript(JURI::root() . "/media/fc/js/general.js", 'text/javascript', true);

    JText::script('COM_REALESTATE_RENTAL_UNSAVED_CHANGES');
  }

}
