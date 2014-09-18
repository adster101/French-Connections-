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
    // Determine the layout we are using.
    // Should this be done with views?
    $view = strtolower(JRequest::getVar('view'));

    $user = JFactory::getUser();
    $userId = $user->id;

    // Get component level permissions
    $canDo = PropertyHelper::getActions();

    JToolBarHelper::title(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_IMAGES_EDIT', $this->property->title, $this->property->realestate_property_id));


    // Cancel out to the helloworld(s) default view rather than the availabilities view...??
    JToolBarHelper::custom('images.saveandnext', 'forward-2', '', 'JTOOLBAR_SAVE_AND_NEXT', false);
    JToolBarHelper::cancel('images.cancel', 'JTOOLBAR_CLOSE');
    JToolBarHelper::custom('unitversions.add', 'plus', '', 'COM_RENTAL_HELLOWORLD_ADD_NEW_UNIT', false);

    //JToolBarHelper::help('', true);
    // Get a toolbar instance so we can append the preview button
    $bar = JToolBar::getInstance('toolbar');
    $property_id = $this->progress[0]->id;
    $bar->appendButton('Preview', 'preview', 'COM_RENTAL_PROPERTY_PREVIEW', $this->property->realestate_property_id);
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
    $document->addScript(JURI::root() . "media/fc/js/libs/blueimp/vendor/jquery.ui.widget.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/libs/blueimp/tmpl.min.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/libs/blueimp/load-image.min.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/libs/blueimp/canvas-to-blob.min.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/libs/blueimp/jquery.iframe-transport.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/libs/blueimp/jquery.fileupload.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/libs/blueimp/jquery.fileupload-process.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/libs/blueimp/jquery.fileupload-image.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/libs/blueimp/jquery.fileupload-validate.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/libs/blueimp/jquery.fileupload-ui.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/main.js", 'text/javascript', true, false);
    // TO DO - Move this to media folder and add to grunt to concat 
    $document->addStyleSheet(JURI::root() . "media/fc/css/jquery.fileupload.css", 'text/css', "screen");
    $document->addStyleSheet(JURI::root() . "media/fc/css/jquery.fileupload-ui.css", 'text/css', "screen");
    $document->addStyleSheet(JURI::root() . "media/fc/css/helloworld.css", 'text/css', "screen");

    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('COM_RENTAL_IMAGES_CONFIRM_DELETE_IMAGE');

    // Register the JHtmlProperty class
    // TO DO - Move this to main or add to concat etc
    JLoader::register('JHtmlFcsortablelist', JPATH_SITE . '/libraries/frenchconnections/helpers/fcsortablelist.php');
  }

}
