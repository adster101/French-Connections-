<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HelloWorlds View
 */
class HelloWorldViewHelloWorlds extends JView
{
	protected $state;

	/**
	 * HelloWorlds view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
    // Find the user details
    $user		= JFactory::getUser();
    $userID = $user->id;
    
		// Get data from the model
		$items = $this->get('Items');

    // Record the number of properties here in the user session scope
    JApplication::setUserState("com_helloworlds_property_count_$userID", count($items));
    
		$pagination = $this->get('Pagination');
 		$this->state		= $this->get('State');
		    
		// Assign data to the view
		$this->items = $items;
		$this->pagination = $pagination;

		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[$item->parent_id][] = $item->id;
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
		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-helloworld {background-image: url(../media/com_helloworld/images/fc-logo-48x48.png);}');

    // Here we register a new JButton which simply uses the ajax squeezebox rather than the iframe handler
    JLoader::register('JButtonAjaxpopup', JPATH_ROOT.'/administrator/components/com_helloworld/buttons/Ajaxpopup.php');
    
		$canDo = HelloWorldHelper::getActions();
		JToolBarHelper::title(JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLDS'), 'helloworld');
		if ($canDo->get('core.create')) 
		{
      // If this is an owner show a modal with additional information
      if (HelloWorldHelper::isOwner())
      {
        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Ajaxpopup', 'new', 'JTOOLBAR_NEW', 'index.php?option=com_helloworld&view=helloworld&layout=new&format=raw');
      } 
      else 
      {
  			JToolBarHelper::addNew('helloworld.add', 'JTOOLBAR_NEW');
      }
    }
		if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) 
		{
			JToolBarHelper::editList('helloworld.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete')) 
		{
			JToolBarHelper::deleteList('', 'helloworlds.delete', 'JTOOLBAR_DELETE');
		}
 
    if($canDo->get('core.edit.state'))
    {
      JToolBarHelper::divider();		  
    	JToolBarHelper::publish('helloworld.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('helloworld.unpublish', 'JTOOLBAR_UNPUBLISH', true);
      JToolBarHelper::trash('helloworlds.trash');      
    }
    
 		if ($canDo->get('core.admin')) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_helloworld');
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
		$document->setTitle(JText::_('COM_HELLOWORLD_ADMINISTRATION'));
    $document->addScript(JURI::root() . "/administrator/components/com_helloworld/js/submitbutton.js");
    $document->addStyleSheet(JURI::root() . "administrator/components/com_helloworld/css/bootstrap-button.css",'text/css',"screen");

		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_UNACCEPTABLE');

	}
}
