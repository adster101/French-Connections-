<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorldList Model
 */
class RealEstateModelPayment extends JModelAdmin
{
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

  /*
   * Method to get the payment form
   *
   */

  public function getPaymentForm($data = array(), $loadData = true)
  {
    JForm::addFormPath(JPATH_LIBRARIES . '/frenchconnections/forms');

    $form = $this->loadForm('com_realestate', 'payment', array('control' => 'jform', 'load_data' => $loadData));

    if (empty($form))
    {
      return false;
    }

    $data = JFactory::getApplication()->getUserState('com_realestate.renewal.data', array());
    $data['id'] = $id = $this->getState($this->getName() . '.id', '');

    $form->bind($data);

    return $form;
  }

  public function getForm($data = array(), $loadData = true)
  {
    JForm::addFormPath(JPATH_LIBRARIES . '/frenchconnections/forms');

    $form = $this->loadForm('account', 'account', array('control' => 'jform', 'load_data' => $loadData));

    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  public function loadFormData()
  {

    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_realestate.edit.listing.data', array());

    // Which layout are we working on?
    $layout = JFactory::getApplication()->input->get('layout', '', 'string');

    // If this is a the payment layout/view then we need to pre-load some data into the form.
    // In particular, we need the property listing id.
    if (empty($data) && $layout == 'payment')
    {
      // Not sure what this is doing!
    }

    return $data;
  }

  public function getTable($type = 'Property', $prefix = 'RealEstateTable', $options = array())
  {
    return JTable::getInstance($type, $prefix, $options);
  }

  public function getBillingDetails($data = array())
  {

    $model = JModelLegacy::getInstance('Property', 'RealEstateModel', array('ignore_request' => true));

    $property_id = $data['id'];

    $property = $model->getItem($property_id);

    $owner_id = $property->created_by;
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
    $data['BillingState'] = $user_data->state;

    return $data;
  }

}
