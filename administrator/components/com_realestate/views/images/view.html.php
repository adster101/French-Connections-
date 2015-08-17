<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RealEstateViewImages extends JViewLegacy
{

  /**
   * display method of Availability View
   * @return void
   */
  public function display($tpl = null)
  {
    $app = JFactory::getApplication();
    $input = $app->input;
    $this->id = $input->get('realestate_property_id');
    $this->state = $this->get('State');

    /*
     *  Following deals with getting the status of the listing
     */

    $this->setModel(JModelLegacy::getInstance('Listing', 'RealEstateModel', array('ignore_request' => true)));

    // Get the listing model so we can get the tab progress detail
    $progress = $this->getModel('Listing');

    $progress->setState('com_realestate.listing.id', $this->id);
    $this->progress = $progress->getItems();
    $this->status = $progress->getProgress($this->progress);

    /*
     * Following deals with getting the listing detail (version, title etc)
     */
    $property = $this->setModel(JModelLegacy::getInstance('PropertyVersions', 'RealEstateModel'));
    $this->property = $property->getItem();

    /*
     * Lastly we see if there are any images saved against this property version...
     */

    $model = $this->getModel();
    $model->setState('version_id', $this->property->id);
    $model->setState('list.limit','');

    $this->items = $model->getItems();

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
   
    JToolBarHelper::title(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_IMAGES_EDIT', $this->property->title, $this->property->realestate_property_id));

    // Cancel out to the helloworld(s) default view rather than the availabilities view...??
    JToolBarHelper::custom('images.saveandnext', 'forward-2', '', 'JTOOLBAR_SAVE_AND_NEXT', false);
    JToolBarHelper::cancel('propertyversions.cancel', 'JTOOLBAR_CLOSE');

    //JToolBarHelper::help('', true);
    // Get a toolbar instance so we can append the preview button
    $bar = JToolBar::getInstance('toolbar');
        
    $bar->appendButton('Preview', 'preview', 'COM_RENTAL_PROPERTY_PREVIEW', $this->property->realestate_property_id,'','com_realestate');
  
  }
  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument()
  {
    $document = JFactory::getDocument();

    JHtml::_('bootstrap.framework');

    $document->setTitle(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_IMAGES_EDIT', $this->property->title, $this->property->realestate_property_id));
       // TO DO - Move this to media folder and add to grunt to concat 
    $document->addStyleSheet("/media/fc/css/jquery.fileupload.css", 'text/css', "screen");
    $document->addStyleSheet("/media/fc/css/jquery.fileupload-ui.css", 'text/css', "screen");
    $document->addStyleSheet("/media/fc/css/helloworld.css", 'text/css', "screen");

    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_IMAGES_CONFIRM_DELETE_IMAGE');

    // Register the JHtmlProperty class
    // TO DO - Move this to main or add to concat etc
    JLoader::register('JHtmlFcsortablelist', JPATH_SITE . '/libraries/frenchconnections/helpers/fcsortablelist.php');
  }

}
