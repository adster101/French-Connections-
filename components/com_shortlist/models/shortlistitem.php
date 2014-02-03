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

    $app = JFactory::getApplication();

    if ($user_id > 0 and $id > 0) {

      // Initialize variables.
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);
      JLog::addLogger(array('text_file' => 'shortlist'), JLog::ALL, array('shortlist'));

      if ($action == 'remove') {
        $query->delete('#__shortlist')
                ->where($db->quotename('user_id') . '=' . (int) $user_id . ' AND ' . $db->quotename('property_id') . '=' . (int) $id);
        // Set the query and execute the delete.
        $db->setQuery($query);

        try {
          $db->execute();
        } catch (RuntimeException $e) {
          // Generate a logger instance for shortlist
          JLog::add('Problem deleting property (' . (int) $id . ') from shortlist for user (' . (int) $user_id . ') - ' . $e->getMessage(), JLog::ALL, 'shortlist');
          return false;
        }

        // Update the session
        $shortlist = $app->getUserState('user.shortlist');

        if ($shortlist) {
          if (array_key_exists($id, $shortlist)) {
            unset($shortlist[$id]);
            $app->setUserState('user.shortlist', $shortlist);
          }
        }

        return true;
      } elseif ($action == 'add') {
        // Create the base insert statement.
        $query->insert($db->quoteName('#__shortlist'))
                ->columns(array($db->quoteName('user_id'), $db->quoteName('property_id'), $db->quoteName('date_created')))
                ->values((int) $user_id . ', ' . (int) $id . ',' . $db->quote(JFactory::getDate()));

        // Set the query and execute the insert.
        $db->setQuery($query);

        try {
          $db->execute();
        } catch (RuntimeException $e) {
          // Generate a logger instance for reviews
          JLog::add('Problem adding property (' . $id . ')to shortlist for user (' . $user_id . ') - ' . $e->getMessage(), JLog::ALL, 'shortlist');
          return false;
        }

        // Update the session
        $shortlist = $app->getUserState('user.shortlist');

        if ($shortlist) {
         if (!array_key_exists($id, $shortlist)) {
            $shortlist[$id] = $id;
            $app->setUserState('user.shortlist', $shortlist);

          }         
        }

        return true;
      }
    }
    return false;
  }

}
