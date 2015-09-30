<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorldList Model
 */
class RentalModelPayment extends JModelAdmin
{
  /*
   * Method to get the payment form
   *
   */

  public function getPaymentForm($data = array(), $loadData = true)
  {
    JForm::addFormPath(JPATH_LIBRARIES . '/frenchconnections/forms');

    $form = $this->loadForm('com_rental.helloworld', 'payment', array('control' => 'jform', 'load_data' => $loadData));

    if (empty($form))
    {
      return false;
    }

    // TO DO - This is a bit messy - most likely should remove this method simply use
    // if then else logic in getForm to determine the form to use. A separate 
    // method could be called to determine form to load based on layout etc
    $data = JFactory::getApplication()->getUserState('com_rental.renewal.data', array());
    $data['id'] = $id = $this->getState($this->getName() . '.id', '');

    $form->bind($data);

    return $form;
  }

  public function getForm($data = array(), $loadData = true)
  {
    JForm::addFormPath(JPATH_LIBRARIES . '/frenchconnections/forms');

    $form = $this->loadForm('com_rental.helloworld', 'account', array('control' => 'jform', 'load_data' => $loadData));

    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  public function getBillingDetails($data = array())
  {

    $model = JModelLegacy::getInstance('Property', 'RentalModel', array('ignore_request' => true));

    $property_id = $data['id'];
    
    $property = $model->getItem($property_id);

    $owner_id = $property->created_by;

    if (!$owner_id)
    {
      // Uh oh, no owner id. With out card billing details payment will fail anyway...
      return $data;
    }

    $user = JFactory::getUser($owner_id);

    // Get the dispatcher and load the user's plugins.
    $dispatcher = JEventDispatcher::getInstance();
    JPluginHelper::importPlugin('user');

    $user_data = new JObject;
    $user_data->id = $owner_id;

    // Trigger the data preparation event.
    $dispatcher->trigger('onContentPrepareData', array('com_users.user', &$user_data));

    $data['BillingFirstnames'] = $user_data->firstname;
    $data['BillingSurname'] = $user_data->surname;
    $data['BillingAddress1'] = $user_data->address1;
    $data['BillingAddress2'] = $user_data->address2;
    $data['BillingCity'] = $user_data->city;
    $data['BillingPostCode'] = $user_data->postal_code;
    $data['BillingEmailAddress'] = $user->email;
    $data['BillingCountry'] = $user_data->country;

    return $data;
  }

  private function _getOWnerID(int $property_id = null)
  {
    
  }

  public function loadFormData()
  {

    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_rental.edit.listing.data', array());

    // Which layout are we working on?
    $layout = JFactory::getApplication()->input->get('layout', '', 'string');

    // If this is a the payment layout/view then we need to pre-load some data into the form.
    // In particular, we need the property listing id.
    if ($layout == 'payment')
    {
      
    }

    return $data;
  }

  /*
   * param JForm $form The JForm instance for the view being edited
   * param array $data The form data as derived from the view (may be empty)
   *
   * @return void
   *
   */

  protected function preprocessForm(JForm $form, $data)
  {

    // Get the input form data 
    $input = JFactory::getApplication()->input;

    // And tease out whether the use_invoice_address field is ticked or not
    $formData = $input->get('jform', array(), 'array');

    $filter = JFilterInput::getInstance();

    $use_invoice_address = $filter->clean($formData['use_invoice_address'], 'int');

    if ($use_invoice_address)
    {
      // Make the billing details optional
      $fieldset = $form->getFieldset('billing-details');

      foreach ($fieldset as $field)
      {
        $form->setFieldAttribute($field->fieldname, 'required', 'false');
      }
    }
  }

  public function getTable($type = 'Property', $prefix = 'RentalTable', $options = array())
  {
    return JTable::getInstance($type, $prefix, $options);
  }

}
