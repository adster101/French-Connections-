<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewOffer extends JViewLegacy
{
	protected $state;

	/**
	 * HelloWorlds view display method
	 * @return void
	 */
	function display($tpl = null) 
	{

    // Get the option parameter
    $option = JRequest::getVar('option', '', 'GET', 'string');

    // Set the item (property) id to the user state set in the offers view
    $this->item->id = JApplication::getUserState("$option.property_id");
    
    
    // Set the offer id (for use in the template)
    $this->item->offer_id = JRequest::getVar('offer_id','','GET','int'); 
    
    JRequest::setVar('id', JApplication::getUserState("$option.property_id"));

    
    // Get data from the model
		$form = $this->get('Form');
 		$script = $this->get('Script');

    // Assign the form data to the view
    $this->form = $form;
    $this->script = $script;

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
		$my_view = 'offers';
		// Add the tabbed submenu for the property edit view.
		RentalHelper::addSubmenu($my_view);
		
		$user = JFactory::getUser();
		$userId = $user->id;
    $property_title = JApplication::getUserState('title'.$this->item->id);
		
    JToolBarHelper::title(JText::sprintf('COM_RENTAL_SPECIAL_OFFERS_EDIT', $property_title), 'helloworld');

    // Built the actions for new and existing records.
    JToolBarHelper::back();
    JToolBarHelper::divider();
    JToolBarHelper::save('offer.save');
    JToolBarHelper::apply('offer.apply', 'JTOOLBAR_APPLY');
 		JToolBarHelper::cancel('offer.cancel', 'JTOOLBAR_CANCEL');
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
		$document->setTitle($isNew ? JText::_('COM_RENTAL_HELLOWORLD_CREATING') : JText::_('COM_RENTAL_HELLOWORLD_EDITING'));
		$document->addScript(JURI::root() . $this->script);
		$document->addScript(JURI::root() . "/administrator/components/com_rental/views/helloworld/submitbutton.js");
		JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
	}
}
