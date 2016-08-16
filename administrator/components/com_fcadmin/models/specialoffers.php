<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class FcadminModelSpecialOffers extends JModelForm
{

  /**
   * Method to get the menu item form.
   *
   * @param   array      $data        Data for the form.
   * @param   boolean    $loadData    True if the form is to load its own data (default case), false if not.
   * @return  JForm    A JForm object on success, false on failure
   * @since   1.6
   */
  public function getForm($data = array(), $loadData = true)
  {
    // Get the form.
    $form = $this->loadForm('com_fcadmin.specialoffers', 'specialoffers', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form))
    {
      return false;
    }

    return $form;
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData()
  {
    // Check the session for queryviously entered form data.
    $data = JFactory::getApplication()->getUserState('com_fcadmin.edit.specialoffers.data', array());

    return $data;
  }

  /**
   * Method to save the form data.
   *
   * @param   array    The form data.
   *
   * @return  boolean  True on success.
   */
  public function save($data)
  {
    $db = JFactory::getDbo();

    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_specialoffers/models');
    $model = JModelLegacy::getInstance('SpecialOffer', 'SpecialOffersModel', $config = array('ignore_request' => true));

    $properties = $this->getPropertyList($data);
    $start_date = JFactory::getDate($data['start_date'])->calendar('Y-m-d');
    $end_date = JFactory::getDate($data['end_date'])->calendar('Y-m-d');

    foreach ($properties as $property)
    {
      try
      {

        // Check if there are any active offers for this unit
        if(!$model->getActiveOffer($property->unit_id, $start_date, $end_date))
        {
          $obj = new stdClass();
          $obj->start_date = $start_date;
          $obj->end_date = $end_date;

          $obj->property_id = $property->id;
          $obj->unit_id = $property->unit_id;

          $obj->title = $data['title'];
          $obj->description = $data['description'];

          $obj->published = 1;
          $obj->status = 1;

          // Insert the offer directly
          $db->insertObject('#__special_offers', $obj);
        }
      }
      catch (Exception $e)
      {
      }
      // Commit the transaction
    }
    return true;
  }

  private function getPropertyList($data = array())
  {
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('a.id, b.id as unit_id')->from($db->quoteName('#__property', 'a'))
          ->where('a.created_by = ' . (int) $data['account'])
          ->where('a.expiry_date > ' . $db->quote(JHtml::_('date', 'now', 'Y-m-d')))
          ->join('left', $db->quoteName('#__unit', 'b') . ' on a.id = b.property_id');
    $db->setQuery($query);

    try {
      $objs = $db->loadObjectList();
    }
    catch (Exception $e) {
      return false;
    }

    return $objs;
  }

}
