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

    // Get the units
    $this->progress = $model->getItems();

    $this->status = $model->getProgress($this->progress);

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

    JToolBarHelper::custom('propertyversions.cancel', 'arrow-left-2', '', 'JTOOLBAR_BACK', false);

    // Built the actions for new and existing records.

    if ($canDo->get('core.create'))
    {
      $bar = JToolbar::getInstance('actions');

      // We can save the new record
      $bar->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'propertyversions.apply', false);
      $bar->appendButton('Standard', 'forward-2', 'JTOOLBAR_SAVE_AND_NEXT', 'propertyversions.saveandnext', false);
      $bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'propertyversions.save', false);
    }

    // Get a toolbar instance so we can append the preview button
    $bar = JToolBar::getInstance('toolbar');
    if ($canDo->get('core.create'))
    {
      // We can save the new record
      $bar->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'propertyversions.apply', false);
      $bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'propertyversions.save', false);
      $bar->appendButton('Standard', 'forward-2', 'JTOOLBAR_SAVE_AND_NEXT', 'propertyversions.saveandnext', false);
    }
    $property_id = $this->status->id;
    $unit_id = $this->progress[0]->unit_id;
    $bar->appendButton('Preview', 'preview', 'COM_RENTAL_PROPERTY_PREVIEW', $property_id, $unit_id);
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
    $document->addScript("/media/fc/js/general.js");

    $document->addScript("//maps.googleapis.com/maps/api/js?key=AIzaSyBudTxPamz_W_Ou72m2Q8onEh10k_yCwYI&sensor=true");
    $document->addScript("/media/fc/js/locate.js", 'text/javascript', true, false);
    $document->addStyleSheet("/media/fc/css/helloworld.css", 'text/css', "screen");
  }

}

