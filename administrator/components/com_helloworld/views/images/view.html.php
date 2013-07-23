<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class HelloWorldViewImages extends JViewLegacy
{
	/**
	 * display method of Availability View
	 * @return void
	 */
	public function display($tpl = null)
	{

    // Add the Listing model to this view, so we can get the progress stuff
    $this->setModel(JModelLegacy::getInstance('Listing', 'HelloWorldModel',array('ignore_request'=>true)));

    // Add the Listing unitversions model to this view, so we can get the unit detail
    $this->setModel(JModelLegacy::getInstance('UnitVersions', 'HelloWorldModel'));

    // Get the unitversions instance so we can get the unit detail
    $unit = $this->getModel('UnitVersions');
    $unit->populateState();
    $this->unit = $unit->getItem();
    

    // Get the listing model so we can get the tab progress detail
    $progress = $this->getModel('Listing');
    $progress->setState('com_helloworld.listing.id',$this->unit->property_id);
    $this->progress = $progress->getItems();

    // populateState for the images model
    $this->state = $this->get('State');
    $images = $this->getModel();
    $images->setState('version_id',$this->unit->id);

    // Get the images associated with this unit version
    $this->items = $this->get('Items');

    $this->pagination = $this->get('Pagination');

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

    // Get component level permissions
		$canDo = HelloWorldHelper::getActions();

    JToolBarHelper::title(($this->unit->unit_title) ? JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $this->unit->unit_title) : JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT'));

 		$bar = JToolBar::getInstance('toolbar');


		// Add a upload button
    if ($canDo->get('helloworld.images.create')) {

			$title = JText::_('JTOOLBAR_UPLOAD');
			$dhtml = "<button data-toggle=\"collapse\" data-target=\"#collapseUpload\" class=\"btn btn-small btn-success\">
						<i class=\"icon-plus icon-white\" title=\"$title\"></i>
						$title</button>";
			//$bar->appendButton('Custom', $dhtml, 'upload');

		}




    // Cancel out to the helloworld(s) default view rather than the availabilities view...??
		JToolBarHelper::cancel('unitversions.cancel', 'JTOOLBAR_CANCEL');

    JToolBarHelper::help('', '');

    // Display a helpful navigation for the owners
    if ($canDo->get('helloworld.ownermenu.view')) {

      $view = strtolower(JRequest::getVar('view'));

      $canDo = HelloWorldHelper::addSubmenu($view);

      // Add the side bar
      $this->sidebar = JHtmlSidebar::render();

    }
  }

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$document = JFactory::getDocument();

    $document->setTitle($this->unit->unit_title ? JText::sprintf('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT', $this->unit->unit_title) : JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLD_EDIT'));

    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/vendor/jquery.ui.widget.js", 'text/javascript', true, false);
    $document->addScript("http://blueimp.github.com/JavaScript-Templates/tmpl.min.js", 'text/javascript', true, false);
    $document->addScript("http://blueimp.github.com/JavaScript-Load-Image/load-image.min.js", 'text/javascript', true, false);
    $document->addScript("http://blueimp.github.com/JavaScript-Canvas-to-Blob/canvas-to-blob.min.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/jquery.iframe-transport.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/jquery.fileupload.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/jquery.fileupload-fp.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/jquery.fileupload-ui.js", 'text/javascript', true, false);
    $document->addScript(JURI::root() . "administrator/components/com_helloworld/js/main.js", 'text/javascript', true, false);


    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/helloworld.css", 'text/css', "screen");
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/jquery.fileupload-ui.css", 'text/css', "screen");
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/helloworld.css",'text/css',"screen");

    JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');
	}
}
