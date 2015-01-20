<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Classification View
 */
class ClassificationViewClassification extends JViewLegacy
{
	/**
	 * display method of Classification view
	 * @return void
	 */
	public function display($tpl = null) 
	{

		// get the Data
		$form = $this->get('Form');
		$item = $this->get('Item');
    
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Assign the Data
		$this->form = $form;
		$this->item = $item;

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
		
		// Eventually figured out that the below hides the submenu on this view.
		//JRequest::setVar('hidemainmenu', true);
		$user = JFactory::getUser();

    $isNew = $this->item->id == 0;
    // TO DO: 
    // Currently ClassificationHelper::getAction only returns the core permissions for the item.
    // Any permissions set at the component level are ignored. Need to fix that.
		$canDo = ClassificationHelper::getActions($this->item->id);
    
    
    JToolBarHelper::title($isNew ? JText::_('COM_RENTAL_MANAGER_HELLOWORLD_NEW') : JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_EDIT', $this->item->title), 'classification');
		// Built the actions for new and existing records.
		if ($isNew) 
		{
			// For new records, check the create permission.
			if ($canDo->get('core.create')) 
			{
				JToolBarHelper::apply('classification.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('classification.save', 'JTOOLBAR_SAVE');
			}
			JToolBarHelper::cancel('classification.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($canDo->get('core.edit'))
			{
				// We can save the new record
				JToolBarHelper::apply('classification.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('classification.save', 'JTOOLBAR_SAVE');
				//JToolBarHelper::custom('classification.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
 
				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create')) 
				{
					JToolBarHelper::custom('classification.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				}
			}
			JToolBarHelper::cancel('classification.cancel', 'JTOOLBAR_CLOSE');
		}
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
		$document->addScript("/administrator/components/com_classification/views/classification/submitbutton.js");

		JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
	}
}
