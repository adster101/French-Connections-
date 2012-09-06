<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HelloWorlds View
 */
class ClassificationViewClassifications extends JView
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

    // Set the toolbar
    $this->addToolBar();
    
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
		
    if ($canDo->get('core.delete')) 
		{
			JToolBarHelper::deleteList('', 'classifications.delete', 'JTOOLBAR_DELETE');
		}

    if ($canDo->get('core.edit.state')) 
		{	
      JToolBarHelper::divider();		  
    	JToolBarHelper::publish('classifications.publish', 'JTOOLBAR_PUBLISH', true);
			JToolBarHelper::unpublish('classifications.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();      
      JToolBarHelper::trash('categories.trash');
    }		
    
    if ($canDo->get('core.admin')) 
		{
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_classification');
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
		$document->addScript(JURI::root() . "/administrator/components/com_classification/views/classifications/submitbutton.js");
		JText::script('COM_HELLOWORLD_HELLOWORLD_ERROR_PARENT_PROPERTY_UNACCEPTABLE');
	}  
  
}