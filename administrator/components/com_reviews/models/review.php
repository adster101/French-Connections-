<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * HelloWorld Model
 */
class ReviewsModelReview extends JModelAdmin
{

  /**
   * Method override to check if you can edit an existing record.
   *
   * @param	array	$data	An array of input data.
   * @param	string	$key	The name of the key for the primary key.
   *
   * @return	boolean
   * @since	1.6
   */
  protected function allowEdit($data = array(), $key = 'id')
  {
    // Check specific edit permission then general edit permission.
    return JFactory::getUser()->authorise('core.edit');
  }

  /**
   * Returns a reference to the a Table object, always creating it.
   *
   * @param	type	The table type to instantiate
   * @param	string	A prefix for the table class name. Optional.
   * @param	array	Configuration array for model. Optional.
   * @return	JTable	A database object
   * @since	1.6
   */
  public function getTable($type = 'Review', $prefix = 'ReviewTable', $config = array())
  {
    return JTable::getInstance($type, $prefix, $config);
  }

  /**
   * Method to get the record form.
   *
   * @param	array	$data		Data for the form.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	mixed	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getForm($data = array(), $loadData = true)
  {

    // Get the form.
    $form = $this->loadForm('com_reviews.reviews', 'review', array('control' => 'jform', 'load_data' => $loadData));
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
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_reviews.edit.review.data', array());

    if (empty($data))
    {
      $data = $this->getItem();
    }

    return $data;
  }

  /**
   * Updates the status and then checks the status of the offer and if being published for the first 
   * time triggers and email to the owner.
   * 
   * @param type $pks
   * @param type $value
   * @return boolean
   */
  public function publish(&$pks, $value = 1)
  {
    $date = JFactory::getDate()->toSql();
    $app = JFactory::getApplication();

    // Load the site from email
    $fromUser = $app->getCfg('mailfrom');
    
    $publish = parent::publish($pks, $value);

    if ($publish)
    {
      // Item has been published, send a notification email to the owner
      foreach ($pks as $k => $v)
      {
        // Get the review details
        $item = $this->getItem($v);

        if (!$item)
        {
          return false;
        }

        $user = JFactory::getUser();

        // Offer already created. If not approved and being set to published then update the approved by gubbins
        if (!$item->approved_by && $value == 1)
        {
          $item->approved_by = $user->id;
          $item->approved_date = $date;

          $table = $this->getTable();

          // Update the offer with the approved by and approved date.
          if (!$table->save($item))
          {
            $this->setError($table->getError());
            return false;
          }

          // Get the owner/property detail
          $property = $this->getPropertyOwner($item->unit_id);
          $owner = JFactory::getUSer($property->created_by);

          // Get the owners email, setting up to go to site mailfrom is debug is on
          $toUser = (JDEBUG) ? $app->getCfg('mailfrom') : $owner->email;

          // Prepare the email.
          $subject = htmlspecialchars(JText::sprintf('COM_REVIEWS_NEW_REVIEW_SUBMITTED', $property->property_id), ENT_QUOTES, 'UTF-8');
          $msg = htmlspecialchars(JText::sprintf('COM_REVIEWS_SUBMISSION_TEXT', $owner->name, $property->property_id, ENT_QUOTES, 'UTF-8'));
          JFactory::getMailer()->sendMail($fromUser, $fromUser, $toUser, $subject, $msg, false);
        }
      }

      return true;
    }
  }

  /**
   * Override save method so we can retrieve the property ID before saving
   */
  public function save($data)
  {

    $app = JFactory::getApplication();
    $table = $this->getTable();
    $key = $table->getKeyName();
    $date = JFactory::getDate()->toSql();

    // Setup a few vars and what not.
    $send_notification_email = false;
    $pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');

    // Get the property details via the unit - only returns property ID and owner acc ID
    $property_details = $this->getPropertyOwner($data['unit_id']);

    // Get the owner details so we know where to send email
    $owner = JFactory::getUser($property_details->created_by);
    // Get the user who is approving the offer
    $user = JFactory::getUser();

    // Load the site from email
    $fromUser = $app->getCfg('mailfrom');

    if (!$data['unit_id'])
    {
      return false;
    }

    // Load the row if saving an existing record. 
    if ($pk > 0)
    {
      $review = $this->getItem($pk);
    }
    else
    {
      $table = $this->getTable();

      // Convert to the JObject before adding other data.
      $properties = $table->getProperties(1);
      $review = JArrayHelper::toObject($properties);
    }

    // If not approved and being set to published then update the approved by gubbins
    if (empty($review->approved_by) && (!$review->published && $data['published'] == 1))
    {
      $data['approved_by'] = $user->id;
      $data['approved_date'] = $date;
      $data['property_id'] = $property_details->property_id;
      // Ensures that we only send notification email when offer is first approved.
      $send_notification_email = true;
    }

    if ($return = parent::save($data))
    {
      // Trigger email to admin user
      if ($send_notification_email)
      {

        // Get the owners email, setting up to go to site mailfrom is debug is on
        $toUser = (JDEBUG) ? $app->getCfg('mailfrom') : $owner->email;

        // Prepare the email.
        $subject = htmlspecialchars(JText::sprintf('COM_REVIEWS_NEW_REVIEW_SUBMITTED', $property_details->property_id), ENT_QUOTES, 'UTF-8');
        $msg = htmlspecialchars(JText::sprintf('COM_REVIEWS_SUBMISSION_TEXT', $owner->name, $property_details->property_id, ENT_QUOTES, 'UTF-8'));
        JFactory::getMailer()->sendMail($fromUser, $fromUser, $toUser, $subject, $msg, true);
      }
    }

    // Save it out
    return $return;
  }

  /**
   * Method to get some basic unit details for use in the confirmation email
   *  
   * @param int $id
   * @return Object on success, false on failure.
   */
  private function getPropertyOwner($unit_id = '')
  {
    $query = $this->_db->getQuery(true);
    $query->select('a.property_id, b.created_by');
    $query->from($this->_db->quoteName('#__unit', 'a'));
    $query->leftJoin($this->_db->quoteName('#__property', 'b') . 'on a.property_id = b.id');
    $query->where('a.id = ' . (int) $unit_id);
    $this->_db->setQuery($query);

    $query = $query->__toString();
    try
    {
      $row = $this->_db->loadObject();
    }
    catch (Exception $e)
    {
      $this->setError($e->getMessage());
      return false;
    }

    return $row;
  }

}