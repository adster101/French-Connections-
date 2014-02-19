<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HelloWorlds View
 */
class InterestingPlacesViewInterestingPlaces extends JViewLegacy
{
	protected $state;
  protected $pagination;
  protected $canDo;
 
  /**
    *  view display method
    * @return void
    */
  function display($tpl = null) 
  {
    // Get data from the model
    $items = $this->get('Items');
    $pagination = $this->get('Pagination');
    $this->state = $this->get('State');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) 
    {
      JError::raiseError(500, implode('<br />', $errors));
      return false;
    }
    // Assign data to the view
    $this->items = $items;
    $this->pagination = $pagination;
 		$this->state		= $this->get('State');
   
    
    // Get the view 
    $view = strtolower(JRequest::getVar('view'));

    // Load the submenu
    InterestingPlacesHelper::addSubmenu($view);
    
    // Set the toolbar
    $this->addToolBar();
    
    // Add the side bar
		$this->sidebar = JHtmlSidebar::render();
    
    // Set the doscument
    $this->setDocument();
    
    // Display the template
    parent::display($tpl);
  }  
  
	/**
	 * Setting the toolbar
	 */
	protected function addToolBar() 
	{			
		$document = JFactory::getDocument();
    
		$canDo = InterestingPlacesHelper::getActions();
		
    // Set the title which appears on the toolbar 
    JToolBarHelper::title(JText::_('COM_INTERESTINGPLACES_MANAGER_CLASSIFICATIONS'));
    		
    if ($canDo->get('core.create')) 
		{
  			JToolBarHelper::addNew('interestingplace.add', 'JTOOLBAR_NEW');
    }
		
    if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) 
		{
			JToolBarHelper::editList('interestingplace.edit', 'JTOOLBAR_EDIT');
		}
		

    if ($canDo->get('core.edit.state')) 
		{	
    	JToolBarHelper::publish('interestingplaces.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('interestingplaces.unpublish', 'JTOOLBAR_UNPUBLISH', true);
      JToolBarHelper::trash('interestingplaces.trash');
 			JToolBarHelper::deleteList('', 'interestingplaces.delete', 'JTOOLBAR_DELETE');
    }		
    
    if ($canDo->get('core.admin')) 
		{
			JToolBarHelper::preferences('com_interestingplaces');
		}
    
    JHtmlSidebar::setAction('index.php?option=com_helloworlds&view=articles');
		
    JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_PUBLISHED'),
			'filter_published',
			JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
    );
    
	}
	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument() 
	{
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_INTERESTINGPLACES_MANAGER_CLASSIFICATIONS'));
		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_PARENT_PROPERTY_UNACCEPTABLE');
	}  
  
}