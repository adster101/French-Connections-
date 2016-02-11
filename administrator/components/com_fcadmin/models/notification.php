<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class FcadminModelNotification extends JModelForm
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
    $form = $this->loadForm('com_fcadmin.notification', 'notification', array('control' => 'jform', 'load_data' => $loadData));
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
    $data = JFactory::getApplication()->getUserState('com_fcadmin.edit.notification.data', array());

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

    $db->transactionStart();
    $objs_to_insert = $this->getUsersToNotify($data);

    try {
      foreach ($objs_to_insert as $obj)
      {
        $db->insertObject('#__messages', $obj);
      }
    }
    catch (Exception $ex) {
      $db->transactionRollback();
      return false;
    }


    $db->transactionCommit();

    return true;
  }

  private function getUsersToNotify($data = array())
  {
    $db = JFactory::getDbo();

    $params = JComponentHelper::getParams('com_fcadmin');
    $users_to_ignore = $params->get('users_to_ignore', '');
    $from = $params->get('notification_from', '');

    $query = $db->getQuery(true);

    $query->select('DISTINCT ' . $from . ' as user_id_from'
                    . ', a.id as user_id_to, now() as date_time,'
                    . $db->quote($data['subject']) . ' as subject,'
                    . $db->quote($data['message']) . ' as message')
            ->from($db->quoteName('#__users', 'a'))
            ->join('left', $db->quoteName('#__property', 'b') . ' on b.created_by = a.id')
            ->where('b.expiry_date > ' . $db->quote(JHtml::_('date', 'now', 'Y-m-d')))
            ->where('a.username not in ( ' . $db->quote($users_to_ignore) . ')');

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
