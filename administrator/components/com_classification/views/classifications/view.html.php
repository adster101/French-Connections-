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
		$document->addStyleDeclaration('.icon-48-helloworld {background-image: url(../media/com_helloworld/images/fc-logo-48x48.png);}');

    // Here we register a new JButton which simply uses the ajax squeezebox rather than the iframe handler
    JLoader::register('JButtonAjaxpopup', JPATH_ROOT.'/administrator/components/com_helloworld/buttons/Ajaxpopup.php');
    
		$canDo = ClassificationHelper::getActions();
		JToolBarHelper::title(JText::_('COM_HELLOWORLD_MANAGER_HELLOWORLDS'), 'helloworld');
		if ($canDo->get('core.create')) 
		{
      
  			JToolBarHelper::addNew('classification.add', 'JTOOLBAR_NEW');
      
    }
		if ($canDo->get('core.edit') || ($canDo->get('core.edit.own'))) 
		{
			JToolBarHelper::editList('helloworld.edit', 'JTOOLBAR_EDIT');
		}
		if ($canDo->get('core.delete')) 
		{
			JToolBarHelper::deleteList('', 'classifications.delete', 'JTOOLBAR_DELETE');
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