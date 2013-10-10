<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HelloWorlds View
 */
class ClassificationViewClassifications extends JViewLegacy
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
    
		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[$item->parent_id][] = $item->id;
		}	
    
    // Get the view 
    $view = strtolower(JRequest::getVar('view'));

    // Load the submenu
    ClassificationHelper::addSubmenu($view);
    
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
    
		$canDo = ClassificationHelper::getActions();
		
    // Set the title which appears on the toolbar 
    JToolBarHelper::title(JText::_('COM_CLASSIFICATION_MANAGER_CLASSIFICATIONS'));
    		
    if ($canDo->get('core.create')) 
		{
  			JToolBarHelper::addNew('classification.add', 'JTOOLBAR_NEW');
    }
		
    if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) 
		{
			JToolBarHelper::editList('classification.edit', 'JTOOLBAR_EDIT');
		}
		

    if ($canDo->get('core.edit.state')) 
		{	
    	JToolBarHelper::publish('classifications.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('classifications.unpublish', 'JTOOLBAR_UNPUBLISH', true);
      JToolBarHelper::trash('classifications.trash');
 			JToolBarHelper::deleteList('', 'classifications.delete', 'JTOOLBAR_DELETE');
    }		
    
    if ($canDo->get('core.admin')) 
		{
 			JToolBarHelper::custom('classifications.rebuild', 'refresh.png', 'refresh_f2.png', 'JTOOLBAR_REBUILD', false);
			JToolBarHelper::preferences('com_classification');
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
		$document->setTitle(JText::_('COM_CLASSIFICATION_MANAGER_CLASSIFICATIONS'));
		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_PARENT_PROPERTY_UNACCEPTABLE');
	}  
  
}