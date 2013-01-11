<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * HelloWorld Controller
 */
class SpecialOffersControllerSpecialOffer extends JControllerForm {

  /**
   * Method override to check if you can edit an existing record.
   *
   * @param	array	$data	An array of input data.
   * @param	string	$key	The name of the key for the primary key.
   *
   * @return	boolean
   * @since	1.6
   */
  protected function allowEdit($data = array(), $key = 'id') {

    // Check specific edit permission then general edit permission.
    if (JFactory::getUser()->authorise('core.edit')) {
      return true;
    }



    return false;
  }

  /*
   * Function to expire a special offer by setting the end_date of the offer to a past date.
   * Offered to owners of an approved (active) offer rather than opening up edit or change state permissions
   * 
   * 
   * 
   */

  public function canceloffer() {
    
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
  
    // Id is the special offer ID
    $input = JFactory::getApplication()->input;

    $ids = $input->post->get('cid',array(),'array');
    $id = $ids[0];
    
    // If there are some IDs to process
    if (count($ids) > 0) {
    
      // Get the user 
      $user = JFactory::getUser();

      // Get the offer details for the offer being edited.
      $table = $this->getModel()->getTable();

      $offer = $table->load($id);

      if (empty($offer)) {
        return false;
      }

      // Should really loop over all ids passed in...
      // Check that this offer is owned by this user (only applies to owners)
      if ($table->created_by === $user->id) {

        // Get the date
        $date = JFactory::getDate();

        // Update the end_date for the offer
        $table->end_date = $date->toSql();

        $table->store($id);

        $this->setMessage(JText::_('COM_SPECIALOFFERS_OFFER_CANCELLED'));

        // redirect back to the list of special offers for this property...
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=' . $this->option, false
                )
        );
      } else {

        $this->setMessage(JText::_('COM_SPECIALOFFERS_YOU_CANNOT_EXPIRE_THIS_OFFER'));
        // redirect back to the list of special offers for this property...
        $this->setRedirect(
                JRoute::_(
                        'index.php?option=' . $this->option, false
                )
        );
      }
  }
    
    return true;
  }

}
