<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewTariffs extends JViewLegacy {

  /**
   * display method of Availability View
   * @return void
   */
  public function display($tpl = null) {
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

    if (empty($listing_id)) {

      // Probably creating a new unit, listing id is in GET scope
      $input = $app->input;
      $listing_id = $input->get('property_id', '', 'int');
    }

    // Set some model options
    $model->setState('com_rental.' . $model->getName() . '.id', $listing_id);
    $model->setState('list.limit', 10);

    // Get the unit progress...
    $this->progress = $model->getItems();


    // Get the unit edit form
    $this->form = $this->get('Form');

    $this->languages = RentalHelper::getLanguages();
    $this->lang = RentalHelper::getLang();

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
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
  protected function addToolBar() {
    // Determine the layout we are using.
    // Should this be done with views?
    $view = strtolower(JRequest::getVar('view'));

    // Get the published state from the form data
    $published = $this->form->getValue('published');

    // Get component level permissions
    $canDo = RentalHelper::getActions();

    JToolBarHelper::title(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_TARIFFS_EDIT', $this->item->unit_title, $this->item->property_id));

    if ($canDo->get('core.edit.own'))
      JToolBarHelper::cancel('tariffs.cancel', 'JTOOLBAR_CLOSE'); {
      // We can save the new record
      JToolBarHelper::apply('tariffs.apply', 'JTOOLBAR_APPLY');
      JToolBarHelper::save('tariffs.save', 'JTOOLBAR_SAVE');
      JToolBarHelper::custom('images.saveandnext', 'forward-2', '', 'JTOOLBAR_SAVE_AND_NEXT', false);
    }

    RentalHelper::addSubmenu('listings');

    // Add the side bar
    $this->sidebar = JHtmlSidebar::render();
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $isNew = $this->item->id == 0;
    $document = JFactory::getDocument();
    $document->setTitle(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_TARIFFS_EDIT', $this->item->unit_title, $this->item->property_id));
    $document->addScript(JURI::root() . "/media/fc/js/general.js");
    JText::script('COM_RENTAL_HELLOWORLD_ERROR_UNACCEPTABLE');
    JText::script('COM_RENTAL_HELLOWORLD_UNSAVED_CHANGES');
    $document->addScript(JURI::root() . "/media/fc/js/general.js");
    $document->addScript(JURI::root() . "administrator/components/com_rental/js/jquery-ui-1.8.23.custom.min.js", 'text/javascript', true);
    $document->addScript(JURI::root() . "administrator/components/com_rental/js/tariffs.js", 'text/javascript', true);
    $document->addStyleSheet(JURI::root() . "administrator/components/com_rental/css/helloworld.css", 'text/css', "screen");
    $document->addStyleSheet(JURI::root() . "administrator/components/com_rental/css/jquery-ui-1.8.23.custom.css", 'text/css', "screen");
  }

}
