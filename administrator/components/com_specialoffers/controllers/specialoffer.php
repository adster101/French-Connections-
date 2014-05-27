<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('frenchconnections.controllers.property.base');

/**
 * HelloWorld Controller
 */
class SpecialOffersControllerSpecialOffer extends RentalControllerBase
{
  /*
   * Function to expire a special offer by setting the end_date of the offer to a past date.
   * Offered to owners of an approved (active) offer rather than opening up edit or change state permissions
   * 
   * 
   * 
   */

  public function canceloffer($key = null)
  {

    // Check for request forgeries.
    JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

    $model = $this->getModel();
    $table = $model->getTable();
    $input = JFactory::getApplication()->input;
    $ids = $input->post->get('cid', array(), 'array');

    // Determine the name of the primary key for the data.
    if (empty($key))
    {
      $key = $table->getKeyName();
    }

    // The record ID of the offer that is being cancelled.
    $recordId = (int) (count($ids) ? $ids[0] : '');

    // Need to refine this so that:
    // i. Additional permission for 'cancel offer' is granted to owners
    // ii.Check that the offer being edited is owned by owner
    if (!$this->allowEdit(array($key => $recordId), $key))
    {
      $this->setMessage(JText::_('COM_SPECIALOFFERS_YOU_CANNOT_EXPIRE_THIS_OFFER'), 'error');
      // redirect back to the list of special offers for this property...
      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option, false
              )
      );

      return false;
    }
    echo "Woot";
    die;

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



    return true;
  }

}
