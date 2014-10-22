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
      // May need to overload getItem to allow the teasing out of the invoice addres details.
      $this->getItem($pk = null);
    }

    return $data;
  }

  public function getTable($type = 'Property', $prefix = 'RealEstateTable', $options = array())
  {
    return JTable::getInstance($type, $prefix, $options);
  }



}