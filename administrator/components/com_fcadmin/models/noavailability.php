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
class FcadminModelNoavailability extends JModelList
{

  protected $ignore_users = array();

  public function __construct($config = array())
  {
    parent::__construct($config);

    // Get the params so we can find users to ignore
    // Put this into a method...probably
    $params = JComponentHelper::getParams('com_fcadmin');

    $ignore = $params->get('users_to_ignore', '');

    $users = explode(',', $ignore);

    foreach ($users as $username)
    {
      $id = JFactory::getUser($username)->id;

      if ($id)
      {
        $this->ignore_users[] = $id;
      }
    }
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

    // Select the required fields from the table.
    // Initialise the query.
    $query->select('
        a.id as PRN,
        h.email,
        replace(e.unit_title, ",","") as unit_title,
        i.firstname,
        h.id as accountID,
        i.sms_alert_number,
        i.sms_valid
      ');
    $query->from('#__property as a');
    $query->join('inner', '#__property_versions as b on (a.id = b.property_id and b.id = (select max(c.id) from #__property_versions as c where c.property_id = a.id and c.review = 0))');
    $query->join('left', '#__unit d on d.property_id = a.id');
    $query->join('left', '#__unit_versions e on (d.id = e.unit_id and e.id = (select max(f.id) from #__unit_versions f where f.unit_id = d.id and f.review = 0))');
    $query->join('left', '#__user_profile_fc g on a.created_by = g.user_id');
    $query->join('left', '#__users h on a.created_by = h.id');
    $query->join('left', '#__user_profile_fc i on i.user_id = h.id');
    $query->where('b.review = 0');
    $query->where('e.review = 0');
    $query->where('d.published != -2');
    // Make sure we only get published units...
    $query->where('d.published = 1');
    $query->where('a.published = 1');
    $query->where('a.created_by !=0');

    // ignore users set in constructor taken from component parameters.
    if (!empty($this->ignore_users))
    {
      $query->where(implode(',', $this->ignore_users));
    }

    $query->where('a.expiry_date >=' . $db->quote(JHtml::_('date', 'now', 'Y-m-d')));
    $query->where('(select count(*) from qitz3_availability where unit_id = d.id and end_date > CURDATE()) = 0');
    $query->order('a.id');

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
