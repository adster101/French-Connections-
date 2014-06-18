<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewOffers extends JViewLegacy
{
	protected $state;

	/**
	 * HelloWorlds view display method
	 * @return void
	 */
	function display($tpl = null) 
	{
    
		// Get special offers for this property by calling getItems method of the model
		$items = $this->get('Items');

    // Get the pagination an state...
    $pagination = $this->get('Pagination');
 		$this->state		= $this->get('State');
    		
		// Assign data to the view
		$this->items = $items;
		$this->pagination = $pagination;
    
    // Get the item (property) ID from the request url...should always be present here 
    // as we are coming in from the property manager
    $this->item->id = JRequest::getVar('id', '', 'GET', 'int');  
    
    // Get the option parameter
    $option = JRequest::getVar('option', '', 'GET', 'string');
    
    // Add the property ID to the user state (i.e. save it in the session)
    JApplication::setUserState("$option.property_id", $this->item->id);
    
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
      die;
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
		// Determine the view we are using. 
		$view = strtolower(JRequest::getVar('view'));
    
		// Add the tabbed submenu for the property edit view.
		RentalHelper::addSubmenu($view);
		
    // Get the property title which is set in the helloworld view...
    $property_title = JApplication::getUserState('title'.$this->item->id);
		
    // Show a helpful toobar title
    JToolBarHelper::title(JText::sprintf('COM_RENTAL_SPECIAL_OFFERS_EDIT', $property_title), 'helloworld');

    // Built the actions for new and existing records.
    // TO DO: make these fine grained by extending joomla assests and permissions?
		JToolBarHelper::editList('offer.edit', 'JTOOLBAR_EDIT');
    JToolBarHelper::addNew('offer.add', 'JTOOLBAR_NEW_OFFER'); 
    JToolBarHelper::divider();
		JToolBarHelper::publish('articles.publish', 'JTOOLBAR_PUBLISH', true);
		JToolBarHelper::unpublish('articles.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::deletelist('offer.delete', 'JTOOLBAR_DELETE');
    JToolBarHelper::cancel('helloworld.cancel', 'JTOOLBAR_CANCEL');
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_RENTAL_OFFERS_MANAGE_OFFERS'));
	}
}
