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

  /**
   * Method to add a new record.
   *
   * @return  mixed  True if the record can be added, a error object if not.
   *
   * @since   12.2
   */
  public function add()
  {
    $app = JFactory::getApplication();
    $context = "$this->option.edit.$this->context";

    // Access check.
    if (!$this->allowAdd())
    {
      // Set the internal error and also the redirect error.
      $this->setError(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
      $this->setMessage($this->getError(), 'error');

      $this->setRedirect(
              JRoute::_(
                      'index.php?option=' . $this->option . '&view=' . $this->view_list
                      . $this->getRedirectToListAppend(), false
              )
      );

      return false;
    }
    
    // Get the model and check if the user is entitled to add anymore...
    $model = $this->getModel();
    
    
    
    
    
    

    // Clear the record edit information from the session.
    $app->setUserState($context . '.data', null);

    // Redirect to the edit screen.
    $this->setRedirect(
            JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend(), false
            )
    );

    return true;
  }

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
    
    // Use the special offer model to look up the item 
    // so we can pump the unit_id into the allowEdit method.
    $item = $model->getItem($ids[0]);
    
    if(!$item)
    {
      return false;
    }
    
    // The record ID of the offer that is being cancelled.
    $recordId = (int) (count($ids) ? $ids[0] : '');

    // Check that the offer can be edited by the owner attempting 
    if (!$this->allowEdit(array($key => $item->unit_id), $key))
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

    if (!$model->canceloffer($recordId))
    {
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



    return true;
  }
  
}
