<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld View
 */
class RentalViewPropertyversions extends JViewLegacy
{

  public function __construct($config = array())
  {
    parent::__construct($config);

    $this->_name = 'propertyversions';
  }

  /**
   * display method of Hello view
   * @return void
   */
  public function display($tpl = null)
  {

    // Get the model state
    $this->state = $this->get('State');

    // get the Data
    $this->form = $this->get('Form');

    $this->item = $this->get('Item');

    $this->script = $this->get('Script');

    // Get an instance of our model, setting ignore_request to true so we bypass units->populateState
    $model = JModelLegacy::getInstance('Listing', 'RentalModel', array('ignore_request' => true));

    // Switch to the revised listing class
    // $model = JModelLegacy::getInstance('Listing_proper', 'RentalModel', array('id' => $this->item->property_id));
    // Here we attempt to wedge some data into the model
    // So another method in the same model can use it.
    $listing_id = ($this->item->property_id) ? $this->item->property_id : '';

    // Set some model options
    $model->setState('com_rental.' . $model->getName() . '.id', $listing_id);
    $model->setState('list.limit', 100);

    $this->progress = $model->getItems();

    $languages = RentalHelper::getLanguages();
    $lang = RentalHelper::getLang();

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }

    // Register the JHtmlProperty class
    JLoader::register('JHtmlProperty', JPATH_COMPONENT . '/helpers/html/property.php');

    // Assign the language data
    $this->languages = $languages;
    $this->lang = $lang;


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
    // Determine the view we are using.
    $view = strtolower(JRequest::getVar('view'));

    // Get the user details
    $user = JFactory::getUser();
    $userId = $user->id;

    // Is this a new property?
    $isNew = $this->item->id == 0;

    // Get component level permissions
    $canDo = RentalHelper::getActions();

    JToolBarHelper::title(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_EDIT', $this->item->property_id));

    // Built the actions for new and existing records.

    if ($canDo->get('core.create'))
    {
      // We can save the new record
      JToolBarHelper::apply('propertyversions.apply', 'JTOOLBAR_APPLY');
      JToolBarHelper::save('propertyversions.save', 'JTOOLBAR_SAVE');
      JToolBarHelper::custom('propertyversions.saveandnext', 'forward-2', '', 'JTOOLBAR_SAVE_AND_NEXT', false);
    }

    JToolbarHelper::help('', false, '/support/rental-property/1139-location-details');

    JToolBarHelper::cancel('propertyversions.cancel', 'JTOOLBAR_CANCEL');


    //RentalHelper::addSubmenu('listings');
    // Add the side bar
    //$this->sidebar = JHtmlSidebar::render();
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument()
  {

    $isNew = $this->item->id == 0;
    $document = JFactory::getDocument();

    $document->setTitle(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_EDIT', $this->item->id));
    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_RENTAL_ERROR_UNACCEPTABLE');
    $document->addScript(JURI::root() . "/media/fc/js/general.js");

    $document->addScript("http://maps.googleapis.com/maps/api/js?key=AIzaSyBudTxPamz_W_Ou72m2Q8onEh10k_yCwYI&sensor=true");
    $document->addScript(JURI::root() . "/administrator/components/com_rental/js/locate.js", 'text/javascript', true, false);
    $document->addStyleSheet(JURI::root() . "/administrator/components/com_rental/css/helloworld.css", 'text/css', "screen");
  }

}

