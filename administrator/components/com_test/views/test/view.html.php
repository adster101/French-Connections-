<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class TestViewTest extends JView
{
	/**
	 * display method of Hello view
	 * @return void
	 */
	public function display($tpl = null) 
	{
				
		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);
 
	}
	
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{
    // Here we register a new JButton which simply uses the ajax squeezebox rather than the iframe handler
    JLoader::register('JButtonAjaxpopup', JPATH_ROOT.'/administrator/components/com_helloworld/buttons/Ajaxpopup.php');
    
    $bar = JToolBar::getInstance('toolbar');
    $bar->appendButton('Ajaxpopup', 'new', 'JTOOLBAR_NEW', 'http://dev.frenchconnections.co.uk/administrator/index.php?option=com_content&view=articles&layout=modal&tmpl=component&function=jSelectArticle_jform_request_id&' . JUtility::getToken() . '=1'); 
	}

}
