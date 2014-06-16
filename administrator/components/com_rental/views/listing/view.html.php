<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class RentalViewListing extends JViewLegacy
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
    $submit = $this->setModel(JModelLegacy::getInstance('Submit', 'RentalModel'));

    // Find the user details
    $user = JFactory::getUser();
    $userID = $user->id;

    // Get the ID
    $app = JFactory::getApplication();
    $this->id = $app->input->get('id', '', 'int');

    // Get data from the model
    $this->items = $this->get('Items');

    $this->pagination = $this->get('Pagination');

    $this->state = $this->get('State');

    $this->form = $submit->getForm($this->items);

    // Register the JHtmlProperty class
    JLoader::register('JHtmlProperty', JPATH_COMPONENT . '/helpers/html/property.php');

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

    $canDo = RentalHelper::getActions();

    JToolBarHelper::title(count($this->items) > 0 ? JText::sprintf('COM_RENTAL_HELLOWORLD_LISTING_TITLE', $this->id) : 'No listings');
    
    JToolBarHelper::back('COM_RENTAL_HELLOWORLD_BACK_TO_PROPERTY_LIST', '/administrator/index.php?option=com_rental');

    if ($layout == 'default')
    {
      // Only show the add units button if there is at least one listing
      if (count($this->items) > 0)
      {



        if ($canDo->get('core.create'))
        {
          //JToolBarHelper::addNew('unitversions.add', 'COM_RENTAL_HELLOWORLD_ADD_NEW_UNIT', false);
        }

        JToolbarHelper::help('asdas', true);
      }
    }

    if ($layout == 'review')
    {
      if ($canDo->get('helloworld.property.review'))
      {
        JToolBarHelper::deleteList('', 'listing.approve', 'BLAH');
      }
    }
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument()
  {
    $document = JFactory::getDocument();

    $document->setTitle(JText::_('COM_RENTAL_ADMINISTRATION'));
    $document->addScript(JURI::root() . "/media/fc/js/general.js", 'text/javascript', true);

    JText::script('COM_RENTAL_HELLOWORLD_ERROR_UNACCEPTABLE');
  }

}
