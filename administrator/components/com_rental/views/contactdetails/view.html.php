<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld View
 */
class RentalViewContactdetails extends JViewLegacy
{

  public function __construct($config = array())
  {
    parent::__construct($config);

    $this->_name = 'contactdetails';
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

    // Get an instance of our model, setting ignore_request to true so we bypass populateState
    $model = JModelLegacy::getInstance('Listing', 'RentalModel', array('ignore_request' => true));

    // Here we attempt to wedge some data into the model
    // So another method in the same model can use it.
    $listing_id = ($this->item->property_id) ? $this->item->property_id : '';

    // Set some model options
    $model->setState('com_rental.' . $model->getName() . '.id', $listing_id);
    $model->setState('list.limit', 100);

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

    JToolBarHelper::title(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_CONTACT_EDIT', $this->item->property_id));

    JToolBarHelper::custom('propertyversions.cancel', 'arrow-left-2', '', 'JTOOLBAR_BACK', false);


    if ($canDo->get('core.edit.own'))
    {
      $bar = JToolbar::getInstance('actions');

      // We can save the new record
      $bar->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'contactdetails.apply', false);

      // TO DO - Work this out to show for new props only
      //if ($this->progress)
      //{
        //$bar->appendButton('Standard', 'forward-2', 'JTOOLBAR_SAVE_AND_NEXT', 'propertyversions.saveandnext', false);
      //}
      
      $bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'contactdetails.save', false);


      // We can save the new record
    }

    // Get a toolbar instance so we can append the preview button
    $bar = JToolBar::getInstance('toolbar');    // Get the property ID as the first item in the progress array
    $property_id = $this->progress[0]->id;
    $unit_id = $this->progress[0]->unit_id;
    $bar->appendButton('Preview', 'preview', 'COM_RENTAL_PROPERTY_PREVIEW', $property_id, $unit_id);

    JToolBarHelper::custom('unitversions.add', 'plus', '', 'COM_RENTAL_HELLOWORLD_ADD_NEW_UNIT', false);

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

    $document->setTitle(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_CONTACT_EDIT', $this->item->property_id));
    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_RENTAL_ERROR_UNACCEPTABLE');
    $document->addScript("/media/fc/js/general.js");
    $document->addStyleSheet("/administrator/components/com_rental/css/helloworld.css", 'text/css', "screen");
  }

}
