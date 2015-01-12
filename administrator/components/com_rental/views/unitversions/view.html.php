<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewUnitversions extends JViewLegacy
{

  /**
   * display method of Hello view
   * @return void
   */
  public function display($tpl = null)
  {

    $app = JFactory::getApplication();

    $layout = $app->input->get('layout', '', 'string');

    // Set the layout property of the unitversions model
    $this->getModel()->layout = $layout;


    $this->state = $this->get('State');

    // Get the unit item...
    $this->item = $this->get('Item');

    $this->item->unit_title = (!empty($this->item->unit_title)) ? $this->item->unit_title : 'New unit';


    // Get an instance of our model, setting ignore_request to true so we bypass units->populateState
    $model = JModelLegacy::getInstance('Listing', 'RentalModel', array('ignore_request' => true));

    // Here we attempt to wedge some data into the model
    // So another method in the same model can use it.
    // If this is a new unit then we don't

    $listing_id = ($this->item->property_id) ? $this->item->property_id : '';

    if (empty($listing_id))
    {

      // Probably creating a new unit, listing id is in GET scope
      $input = $app->input;
      $listing_id = $input->get('property_id', '', 'int');
    }

    // Set some model options
    $model->setState('com_rental.' . $model->getName() . '.id', $listing_id);
    $model->setState('list.limit', 10);

    // Get the unit progress...
    $this->progress = $model->getItems();

    $this->status = $model->getProgress($this->progress);

    // Get the unit edit form
    $this->form = $this->get('Form');

    $this->languages = RentalHelper::getLanguages();
    $this->lang = RentalHelper::getLang();

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
    // Determine the layout we are using.
    // Should this be done with views?
    $view = strtolower(JRequest::getVar('view'));

    $user = JFactory::getUser();
    $userId = $user->id;

    $isNew = $this->item->id == 0;

    // Get component level permissions
    $canDo = RentalHelper::getActions();

    JToolBarHelper::title(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_UNIT_EDIT', $this->item->unit_title, $this->item->property_id));

    JToolBarHelper::custom('propertyversions.cancel', 'arrow-left-2', '', 'JTOOLBAR_BACK', false);

    // Built the actions for new and existing records.
    // For new records, check the create permission.
    if ($canDo->get('core.create'))
    {
      $bar = JToolbar::getInstance('actions');

      // We can save the new record
      $bar->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'unitversions.apply', false);
      $bar->appendButton('Standard', 'forward-2', 'JTOOLBAR_SAVE_AND_NEXT', 'unitversions.saveandnext', false);
      $bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'unitversions.save', false);
    }

    // Get a toolbar instance so we can append the preview button
    $bar = JToolBar::getInstance('toolbar');
    $property_id = $this->progress[0]->id;
    $unit_id = $this->progress[0]->unit_id;
    $bar->appendButton('Preview', 'preview', 'COM_RENTAL_PROPERTY_PREVIEW', $property_id, $unit_id);

    JToolBarHelper::custom('unitversions.add', 'plus', '', 'COM_RENTAL_HELLOWORLD_ADD_NEW_UNIT', false);

    $help = JToolbar::getInstance('help');
    $help->appendButton('Help', 'unitversions', true);
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
    $document->setTitle($isNew ? JText::_('COM_RENTAL_HELLOWORLD_NEW_UNIT_EDIT') : JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_UNIT_EDIT', $this->item->unit_title, $this->item->property_id) );
    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_RENTAL_ERROR_UNACCEPTABLE');
    $document->addScript("/media/fc/js/general.js");
    //$document->addStyleSheet("/administrator/components/com_rental/css/helloworld.css", 'text/css', "screen");
    //$document->addScript("/administrator/components/com_rental/js/jquery-ui-1.8.23.custom.min.js", 'text/javascript', true);
    //$document->addScript("/administrator/components/com_rental/js/tariffs.js", 'text/javascript', true);
    //$document->addStyleSheet("/administrator/components/com_rental/css/jquery-ui-1.8.23.custom.css", 'text/css', "screen");
  }
}
