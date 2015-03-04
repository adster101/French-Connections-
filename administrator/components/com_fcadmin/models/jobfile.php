<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Methods supporting a list of tracks.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 * @since       1.6
 */
class FcadminModelJobfile extends JModelList
{

  public function getTotal()
  {
    // Seems to cause a problem with the union below...
    //parent::getTotal();
  }

  /**
   * Build an SQL query to load the list data.
   *
   * @return  JDatabaseQuery
   *
   * @since   1.6
   */
  protected function getListQuery()
  {

    // Create a new query object.
    $db = $this->getDbo();
    $query = $db->getQuery(true);
    // Array of user to ignore - mainly affiliates...
    $users_to_ignore = array();
    $users_to_ignore[] = JUser::getInstance('allezfrancais')->id;
    $users_to_ignore[] = JUser::getInstance('frueda@realestatelanguedoc.com')->id;
    $users_to_ignore[] = JUser::getInstance('atleisure')->id;

    // Select the required fields from the table.
    $query->select('
      a.id as JobNo,
      concat("PRN: ", a.id, " owned by ", b.firstname, " ", b.surname, "(", a.created_by, ")") as JobName,
      a.id as SubJobOf,
      "D" as Header,
      concat("PRN: ", a.id, " owned by ", b.firstname, " ", b.surname, "(", a.created_by, ")") as Description,
      "" as Contact,
      "0%" as PercentComplete,
      "" as StartDate,
      "" as FinishDate, 
      "" as Manager,
      concat(b.surname, " (", a.created_by, "), ", b.firstname) as LinkedCustomer,
      "N" as InactiveJob,  
      "N" as TrackReimbursables
    ');
    $query->from($db->quoteName('#__property', 'a'));
    $query->leftJoin($db->quoteName('#__user_profile_fc', 'b') . ' on a.created_by = b.user_id');
    $query->where('a.created_by not in (' . implode(',', $users_to_ignore) . ')');

    // Cretae a second query to union with the first...
    $query2 = $db->getQuery(true);

    // Select the required fields from the table.
    $query2->select('
      a.id as JobNo,
      concat("PRN: ", a.id, " owned by ", b.firstname, " ", b.surname, "(", a.created_by, ")") as JobName,
      a.id as SubJobOf,
      "D" as Header,
      concat("PRN: ", a.id, " owned by ", b.firstname, " ", b.surname, "(", a.created_by, ")") as Description,
      "" as Contact,
      "0%" as PercentComplete,
      "" as StartDate,
      "" as FinishDate, 
      "" as Manager,
      concat(b.surname, " (", a.created_by, "), ", b.firstname) as LinkedCustomer,
      "N" as InactiveJob,  
      "N" as TrackReimbursables
    ');
    $query2->from($db->quoteName('#__realestate_property', 'a'));
    $query2->leftJoin($db->quoteName('#__user_profile_fc', 'b') . ' on a.created_by = b.user_id');
    $query2->where('a.created_by not in (' . implode(',', $users_to_ignore) . ')');

    $query2->order('a.id', 'asc');

    $query->union($query2);

    return $query;
  }

  /**
   * Get the content
   *
   * @return  string    The content.
   *
   * @since   1.6
   */
  public function getContent()
  {
    if (!isset($this->content))
    {

      $items = $this->getItems();

      $this->content = '';

      foreach ($items[0] as $key => $value)
      {
        $this->content .= $key . "\t";
      }

      $this->content .= "\r\n";

      foreach ($items as $item)
      {
        $bits = JArrayHelper::fromObject($item);
        $this->content .= implode("\t", $bits) . "\r\n";
      }
    }

    return $this->content;
  }

  public function populateState($ordering = null, $direction = null)
  {

    parent::populateState($ordering, $direction);

    $this->setState('list.limit', 0);
  }

}