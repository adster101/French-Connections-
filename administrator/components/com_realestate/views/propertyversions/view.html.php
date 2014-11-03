<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HelloWorld View
 */
class RealEstateViewPropertyversions extends JViewLegacy
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

    // Get an instance of our listing model, setting ignore_request to true so we bypass units->populateState
    $model = JModelLegacy::getInstance('Listing', 'RealEstateModel', array('ignore_request' => true));

    // Set some model options
    $model->setState('com_realestate.' . $model->getName() . '.id', $this->item->realestate_property_id);
    $model->setState('list.limit', 100);

    // Get the units
    $this->progress = $model->getItems();


    $this->status = $model->getProgress($this->progress);

    // Check for errors.
    if (count($errors = $this->get('Errors')))
    {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }

    // Register the JHtmlProperty class
    JLoader::register('JHtmlProperty', JPATH_COMPONENT . '/helpers/html/property.php');

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
    $canDo = PropertyHelper::getActions();

    JToolBarHelper::title(JText::sprintf('COM_REALESTATE_MANAGER_PROPERTY_EDIT', $this->item->realestate_property_id));

    // Built the actions for new and existing records.

    if ($canDo->get('core.create') && !empty($this->status->expiry_date))
    {
      // We can save the new record
      JToolBarHelper::apply('propertyversions.apply', 'JTOOLBAR_APPLY');
      JToolBarHelper::save('propertyversions.save', 'JTOOLBAR_SAVE');
      JToolBarHelper::custom('propertyversions.saveandnext', 'forward-2', '', 'JTOOLBAR_SAVE_AND_NEXT', false);
    }
    elseif ($canDo->get('core.create'))
    {
      JToolBarHelper::apply('propertyversions.apply', 'JTOOLBAR_APPLY');
      JToolBarHelper::custom('propertyversions.saveandnext', 'forward-2', '', 'JTOOLBAR_SAVE_AND_NEXT', false);
    }

    // Get a toolbar instance so we can append the preview button
    $bar = JToolBar::getInstance('toolbar');

    $bar->appendButton('Preview', 'preview', 'COM_RENTAL_PROPERTY_PREVIEW', $this->item->realestate_property_id,'','com_realestate');


    JToolBarHelper::cancel('propertyversions.cancel', 'JTOOLBAR_CLOSE');

    //PropertyHelper::addSubmenu('listings');
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

    $document->setTitle(JText::sprintf('COM_REALESTATE_MANAGER_HELLOWORLD_EDIT', $this->item->id));
    JText::script('COM_REALESTATE_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_RENTAL_ERROR_UNACCEPTABLE');
    $document->addScript(JURI::root() . "/media/fc/js/general.js", 'text/javascript', true, false);

    $document->addScript("http://maps.googleapis.com/maps/api/js?key=AIzaSyBudTxPamz_W_Ou72m2Q8onEh10k_yCwYI&sensor=true");
    $document->addScript(JURI::root() . "/media/fc/js/locate.js", 'text/javascript', true, false);
    $document->addStyleSheet(JURI::root() . "/media/fc/css/helloworld.css", 'text/css', "screen");
  }

}

