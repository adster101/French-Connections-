<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewTariffs extends JViewLegacy
{

    /**
     * display method of Availability View
     * @return void
     */
    public function display($tpl = null)
    {
        $app = JFactory::getApplication();

        $layout = $app->input->get('layout', '', 'string');

        // Set the layout property of the unitversions model
        $this->getModel()->layout = $layout;

        $this->state = $this->get('State');

        // Get the unit item...
        $this->item = $this->get('Item');

        $this->item->unit_title = (!empty($this->item->unit_title)) ? $this->item->unit_title : 'New unit';

        // Get an instance of our model, setting ignore_request to true so we bypass units->populateState
        $model = JModelLegacy::getInstance('Listing', 'RentalModel', array('ignore_request' => true));

        // Here we attempt to wedge some data into the model
        // So another method in the same model can use it.
        // If this is a new unit then we don't

        $listing_id = ($this->item->property_id) ? $this->item->property_id : '';

        if (empty($listing_id))
        {

            // Probably creating a new unit, listing id is in GET scope
            $input = $app->input;
            $listing_id = $input->get('property_id', '', 'int');
        }

        // Set some model options
        $model->setState('com_rental.' . $model->getName() . '.id', $listing_id);
        $model->setState('list.limit', 10);

        // Get the unit progress...
        $this->progress = $model->getItems();

        $this->status = $model->getProgress($this->progress);


        // Get the unit edit form
        $this->form = $this->get('Form');

        $this->languages = RentalHelper::getLanguages();
        $this->lang = RentalHelper::getLang();

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
        // Should this be done with views?
        $view = strtolower(JRequest::getVar('view'));

        // Get the published state from the form data
        $published = $this->form->getValue('published');

        // Get component level permissions
        $canDo = RentalHelper::getActions();

        JToolBarHelper::title(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_TARIFFS_EDIT', $this->item->unit_title, $this->item->property_id));

        // Add the back 'cancel' button
        JToolBarHelper::custom('tariffs.cancel', 'arrow-left-2', '', 'JTOOLBAR_BACK', false);

        if ($canDo->get('core.edit.own'))
        {
            $bar = JToolbar::getInstance('actions');

            // We can save the new record
            $bar->appendButton('Standard', 'apply', 'JTOOLBAR_APPLY', 'tariffs.apply', false);
            $bar->appendButton('Standard', 'forward-2', 'JTOOLBAR_SAVE_AND_NEXT', 'tariffs.saveandnext', false);
            $bar->appendButton('Standard', 'save', 'JTOOLBAR_SAVE', 'tariffs.save', false);

            JToolBarHelper::apply('tariffs.apply', 'JTOOLBAR_APPLY');
            JToolBarHelper::save('tariffs.save', 'JTOOLBAR_SAVE');
            JToolBarHelper::custom('tariffs.saveandnext', 'forward-2', '', 'JTOOLBAR_SAVE_AND_NEXT', false);
            //JToolBarHelper::cancel('tariffs.cancel', 'JTOOLBAR_CLOSE');
        }

        // Get a toolbar instance so we can append the preview button
        $bar = JToolBar::getInstance('toolbar');
        $property_id = $this->progress[0]->id;
        $unit_id = $this->progress[0]->unit_id;
        $bar->appendButton('Preview', 'preview', 'COM_RENTAL_PROPERTY_PREVIEW', $property_id, $unit_id);

        //JToolBarHelper::help('', true);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::sprintf('COM_RENTAL_MANAGER_HELLOWORLD_TARIFFS_EDIT', $this->item->unit_title, $this->item->property_id));
        JText::script('COM_RENTAL_RENTAL_UNSAVED_CHANGES');
        JText::script('JGLOBAL_VALIDATION_FORM_FAILED');
        JText::script('COM_RENTAL_IMAGES_CONFIRM_DELETE_ITEM');

        $document->addStyleSheet("/media/fc/css/helloworld.css", 'text/css', "screen");
        $document->addStyleSheet("/media/fc/css/jquery-ui-1.8.23.custom.css", 'text/css', "screen");
    }

}
