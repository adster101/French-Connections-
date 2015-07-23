<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewImages extends JViewLegacy
{

  /**
   * display method of Availability View
   * @return void
   */
  public function display($tpl = null)
  {
    // Add the Listing model to this view, so we can get the progress stuff
    $this->setModel(JModelLegacy::getInstance('Listing', 'RentalModel', array('ignore_request' => true)));

    // Add the Listing unitversions model to this view, so we can get the unit detail
    $this->setModel(JModelLegacy::getInstance('UnitVersions', 'RentalModel'));

    // Get the unitversions instance so we can get the unit detail
    $unit = $this->getModel('UnitVersions');
    $this->unit = $unit->getItem();
    $this->unit->unit_title = (!empty($this->unit->unit_title)) ? $this->unit->unit_title : 'New unit';

    // Get the listing model so we can get the tab progress detail
    $progress = $this->getModel('Listing');
    $progress->setState('com_rental.listing.id', $this->unit->property_id);
    $this->progress = $progress->getItems();

    $this->status = $progress->getProgress($this->progress);

    $this->property_id = $this->progress[0]->id;

    // populateState for the images model
    $this->state = $this->get('State');
    $images = $this->getModel();
    $images->setState('version_id', $this->unit->id);

    // Set the list limit model state so that we return all available images.
    $images->setState('list.limit');

    // Get the images associated with this unit version
    $this->items = $this->get('Items');

    $this->pagination = $this->get('Pagination');

    // Set the toolbar
    $this->addToolBar();
    // Set the document
    $this->setDocument();

    // Display the template
    parent::display($tpl);
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

    // Get component level permissions
    $canDo = RentalHelper::getActions();

    JToolBarHelper::title(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_IMAGES_EDIT', $this->unit->unit_title, $this->unit->property_id));
    JToolBarHelper::custom('images.cancel', 'arrow-left-2', '', 'JTOOLBAR_BACK', false);
    $bar = JToolbar::getInstance('actions');

    // We can save the new record
    $bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'images.cancel', false);
    $bar->appendButton('Standard', 'forward-2', 'JTOOLBAR_SAVE_AND_NEXT', 'images.saveandnext', false);

    // Cancel out to the helloworld(s) default view rather than the availabilities view...??
    //JToolBarHelper::help('', true);
    // Get a toolbar instance so we can append the preview button
    $bar = JToolBar::getInstance('toolbar');
    $property_id = $this->progress[0]->id;
    $unit_id = $this->progress[0]->unit_id;
    $bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'images.cancel', false);
    $bar->appendButton('Standard', 'forward-2', 'JTOOLBAR_SAVE_AND_NEXT', 'images.saveandnext', false);

    $bar->appendButton('Preview', 'preview', 'COM_RENTAL_PROPERTY_PREVIEW', $this->property_id, $this->unit->unit_id);
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument()
  {
    $document = JFactory::getDocument();
    // Add the live chat script, or not!
    RentalHelper::addLiveChat($this->status->expiry_date);

    JHtml::_('bootstrap.framework');

    $document->setTitle(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_IMAGES_EDIT', $this->unit->unit_title, $this->unit->property_id));
        // TO DO - Move this to media folder and add to grunt to concat 
    $document->addStyleSheet("/media/fc/css/jquery.fileupload.css", 'text/css', "screen");
    $document->addStyleSheet("/media/fc/css/jquery.fileupload-ui.css", 'text/css', "screen");
    $document->addStyleSheet("/media/fc/css/helloworld.css", 'text/css', "screen");

    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_IMAGES_CONFIRM_DELETE_IMAGE');

    // Register the JHtmlProperty class
    // TO DO - Move this to main or add to concat etc
    JLoader::register('JHtmlFcsortablelist', JPATH_SITE . '/libraries/frenchconnections/helpers/fcsortablelist.php');
  }

}
