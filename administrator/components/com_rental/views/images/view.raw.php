<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HelloWorld View
 */
class RentalViewImages extends JViewLegacy
{
	/**
	 * display method of Availability View
	 * @return void
	 */
	public function display($tpl = null)
	{
    

    $input = JFactory::getApplication()->input;
    $id = $input->get('id','','int');
 

     
    // populateState for the images model
    $this->state = $this->get('State');
    $images = $this->getModel();
    $images->setState('version_id',$id);
    // Set the list limit model state so that we return all available images.
    $images->setState('list.limit');

    // Assign the Item
		$this->items = $this->get('Items');


    
    $this->pagination = $this->get('Pagination');

		// Display the template
		parent::display($tpl);

	}

}
