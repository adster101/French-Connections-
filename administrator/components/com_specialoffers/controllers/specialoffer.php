<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('frenchconnections.controllers.property.base');

/**
 * HelloWorld Controller
 */
class SpecialOffersControllerSpecialOffer extends HelloWorldControllerBase {
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

    $model = $this->getModel();
    $table = $model->getTable();
    // Get the user 
    $user = JFactory::getUser();
    $input = JFactory::getApplication()->input;
    $ids = $input->post->get('cid', array(), 'array');


    // If there are some IDs to process
    if (count($ids) > 0) {

      // Get the offer details for the offer being edited.
      $table = $this->getModel()->getTable();

      foreach ($ids as $id) {

        if (!$table->load($id)) {
          continue;
        }

        /*
         * The all important property id should now be set in the table object
         */
        $property_id = ($table->property_id) ? $table->property_id : '';

        // Need to refine this so that:
        // i. Additional permission for 'cancel offer' is granted to owners
        // ii.Check that the offer being edited is owned by owner 
        if (!$this->allowEdit(array('property_id' => $property_id))) {

          $this->setMessage(JText::_('COM_SPECIALOFFERS_YOU_CANNOT_EXPIRE_THIS_OFFER'),'error');
          // redirect back to the list of special offers for this property...
          $this->setRedirect(
                  JRoute::_(
                          'index.php?option=' . $this->option, false
                  )
          );

          return false;
        }

        // Update the end_date for the offer
        $table->end_date = JFactory::getDate()->toSql();

        $table->store($id);

        $this->setMessage(JText::_('COM_SPECIALOFFERS_OFFER_CANCELLED'));

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
