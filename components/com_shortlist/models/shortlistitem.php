<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

/**
 * HelloWorld Model
 */
class ShortlistModelShortlistItem extends JModelItem {

  /**
   * updateShortList - either adds or removes the user_id.property_id to the shortlist table 
   * 
   * @param type $user_id
   * @param type $id
   * @param type $action
   * @return boolean
   */
  public function updateShortlist($user_id = '', $id = '', $action = 'remove') {

    if ($user_id > 0 and $id > 0) {

      // Initialize variables.
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      JLog::addLogger(array('text_file' => 'shortlist'), JLog::ALL, array('shortlist'));

      if ($action == 'remove') {
        $query->delete('#__shortlist')
                ->where($db->quotename('user_id') . '=' . (int) $user_id . ' AND ' . $db->quotename('property_id') . '=' . $id);
        // Set the query and execute the delete.
        $db->setQuery($query);

        try {
          $db->execute();
        } catch (RuntimeException $e) {
          // Generate a logger instance for shortlist
          JLog::add('Problem deleting property (' . $id . ') from shortlist for user (' . $user_id . ') - ' . $e->getMessage(), JLog::ALL, 'shortlist');
          return false;
        }

        return true;
      } elseif ($action == 'add') {
        // Create the base insert statement.
        $query->insert($db->quoteName('#__shortlist'))
                ->columns(array($db->quoteName('user_id'), $db->quoteName('property_id'), $db->quoteName('date_created')))
                ->values((int) $user_id . ', ' . $id . ',' . $db->quote(JFactory::getDate()));

        // Set the query and execute the insert.
        $db->setQuery($query);

        try {
          $db->execute();
        } catch (RuntimeException $e) {
          // Generate a logger instance for reviews
          JLog::add('Problem adding property (' . $id . ')to shortlist for user (' . $user_id . ') - ' . $e->getMessage(), JLog::ALL, 'shortlist');
          return false;
        }

        return true;
      }
    }
    return false;
  }

}
