<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorlds View
 */
class RentalViewAutorenewals extends JViewLegacy
{
  protected $items;
  protected $state;
  protected $pagination;
  /**
   * HelloWorld raw view display method
   * This is used to check how many properties the user has
   * when they click 'new' on the property manager.
   *
   * @return void
   */
  function display($tpl = null)
  {

    $app = JFactory::getApplication();
    $input = $app->input;

    $this->extension = $input->get('option', '', 'string');

    $this->id = $input->get('id', '', 'int');

    // Get the property listing details...
    $this->items = $this->get('Items');

    // Set the document
    $this->setDocument();

    // Set the document
    $this->addToolBar();

    // Display the template
    parent::display($tpl);
  }

  /**
   * Method to set up the document properties
   *
   * @return void
   */
  protected function setDocument()
  {
    JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
    JText::script('JGLOBAL_VALIDATION_FORM_FAILED');
    $document = JFactory::getDocument();
    $document->setTitle(JText::_('COM_RENTAL_ADMINISTRATION'));
  }

  /**
   * Setting the toolbar
   */
  protected function addToolBar()
  {

    // Get component level permissions
    $canDo = RentalHelper::getActions();

    JToolBarHelper::title(($this->id) ? JText::sprintf('COM_RENTAL_HELLOWORLD_MANAGE_AUTO_RENEWAL', $this->id) : JText::_('COM_RENTAL_MANAGER_HELLOWORLD_NEW'));
    // TO DO - For owners back should be to OA homepage, probably taken care of by permissions settings
    JToolBarHelper::back('JTOOLBAR_BACK', '/administrator/index.php?option=com_rental');

    if ($canDo->get('core.edit.state'))
		{
			JToolbarHelper::makeDefault('autorenewals.setDefault');
			JToolbarHelper::divider();
		}

    if ($canDo->get('core.delete'))
    {
      JToolBarHelper::deleteList('Are you sure?', 'autorenewals.delete', 'JTOOLBAR_DELETE');
    }

    $bar = JToolbar::getInstance('actions');

    $bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'autorenewals.save', false);

    // We can save the new record
    //JToolBarHelper::help('COM_RENTAL_HELLOWORLD_NEW_PROPERTY_HELP_VIEW', true);
  }

}
