<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewImages extends JViewLegacy {

  /**
   * display method of Availability View
   * @return void
   */
  public function display($tpl = null) {

    // Add the Listing model to this view, so we can get the progress stuff
    $this->setModel(JModelLegacy::getInstance('Listing', 'HelloWorldModel', array('ignore_request' => true)));

    // Add the Listing unitversions model to this view, so we can get the unit detail
    $this->setModel(JModelLegacy::getInstance('UnitVersions', 'HelloWorldModel'));

    // Get the unitversions instance so we can get the unit detail
    $unit = $this->getModel('UnitVersions');
    $this->unit = $unit->getItem();
    $this->unit->unit_title = (!empty($this->unit->unit_title)) ? $this->unit->unit_title : 'New unit';

    // Get the listing model so we can get the tab progress detail
    $progress = $this->getModel('Listing');
    $progress->setState('com_helloworld.listing.id', $this->unit->property_id);
    $this->progress = $progress->getItems();

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
  protected function addToolBar() {
    // Determine the layout we are using.
    // Should this be done with views?
    $view = strtolower(JRequest::getVar('view'));

    $user = JFactory::getUser();
    $userId = $user->id;

    // Get component level permissions
    $canDo = HelloWorldHelper::getActions();

    JToolBarHelper::title( JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_IMAGES_EDIT', $this->unit->unit_title, $this->unit->property_id));

    $bar = JToolBar::getInstance('toolbar');

    // Add a upload button
    if ($canDo->get('helloworld.images.create')) {

      $title = JText::_('JTOOLBAR_UPLOAD');
      $dhtml = "<button data-toggle=\"collapse\" data-target=\"#collapseUpload\" class=\"btn btn-small btn-success\">
						<i class=\"icon-plus icon-white\" title=\"$title\"></i>
						$title</button>";
    }

    // Cancel out to the helloworld(s) default view rather than the availabilities view...??
    JToolBarHelper::cancel('unitversions.cancel', 'JTOOLBAR_CANCEL');

    JToolBarHelper::help('', '');

    $canDo = HelloWorldHelper::addSubmenu('listings');

    // Add the side bar
    $this->sidebar = JHtmlSidebar::render();
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument() {
    $document = JFactory::getDocument();

    $document->setTitle($this->unit->unit_title ? JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $this->unit->unit_title) : JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT'));

    $document->addScript(JURI::root() . "media/fc/js/images/vendor/jquery.ui.widget.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/images/tmpl.min.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/images/load-image.min.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/images/canvas-to-blob.min.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/images/jquery.iframe-transport.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/images/jquery.fileupload.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/images/jquery.fileupload-process.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/images/jquery.fileupload-image.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/images/jquery.fileupload-validate.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/images/jquery.fileupload-ui.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/images/main.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "media/fc/js/general.js", 'text/javascript', true);
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/helloworld.css", 'text/css', "screen");
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/jquery.fileupload-ui.css", 'text/css', "screen");
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/helloworld.css", 'text/css', "screen");

    JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
    JText::script('COM_HELLOWORLD_HELLOWORLD_UNSAVED_CHANGES');

    // Register the JHtmlProperty class
    JLoader::register('JHtmlFcsortablelist', JPATH_SITE . '/libraries/frenchconnections/helpers/fcsortablelist.php');
  }

}
