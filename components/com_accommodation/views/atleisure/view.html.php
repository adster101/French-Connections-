<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the HelloWorld Component
 */
class AccommodationViewAtleisure extends JViewLegacy
{

    // Overwriting JView display method
    function display($tpl = null)
    {

        // Assign data to the view   
        $app = JFactory::getApplication();

        // TO DO - Set it to redirect to the property page...
        if (!$app->getUserState('com_accommodation.atleisure.data'))
        {
            $app->redirect('/');
        }

        // Set the default model to the listing model
        $model = $this->setModel(JModelLegacy::getInstance('Listing', 'AccommodationModel'), true);

        if (!$this->item = $this->get('Item'))
        {
            throw new Exception(JText::_('WOOT'), 410);
        }

        $this->images = $this->get('Images');

        // Get the location breadcrumb trail
        $this->crumbs = $this->get('Crumbs');

        // Get the booking detail form
        $this->form = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseWarning(404, implode("\n", $errors));
            return false;
        }

        // Configure the pathway.
        if (!empty($this->crumbs))
        {
            $app->getPathWay()->setPathway($this->crumbs);
        }

        // Set the document
        $this->setDocument();

        // Display the view
        parent::display($tpl);
    }

    /**
     * Method to set up the document properties
     * TO DO - Move this into the model?
     *
     * @return void
     */
    protected function setDocument()
    {
        $app = JFactory::getApplication();

        $layout = $app->input->getCmd('layout', 'default');

        if ($layout == 'default')
        {
            $this->title = JText::_('COM_ACCOMMODATION_AT_LEISURE_BOOKING_PAGE_YOUR_DETAILS');
        }
        elseif ($layout == 'payment')
        {
            $this->title = JText::_('COM_ACCOMMODATION_AT_LEISURE_BOOKING_PAGE_PAYMENT_DETAILS');
        }
        
        // Set document and page titles
        $this->document->setTitle($this->title);
        $this->document->setDescription($this->title);
        $this->document->setMetaData('keywords', $this->title);
        $this->document->setMetaData('robots', 'noindex');
    }

}
