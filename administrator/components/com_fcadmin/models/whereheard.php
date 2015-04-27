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
class FcadminModelWhereheard extends JModelList
{

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
    $select = '';
    
    for ($x = 6; $x >= 0; $x--)
    {
      $int = '-' . $x . ' MONTH';
      $year = JHtml::_('date', $int, 'Y');
      $month = JHtml::_('date', $int, 'm');
      $day = JHtml::_('date', $int, 'd');

      $date = mktime(0, 0, 0, $month, $day, $year);

      $select .= ",SUM( CASE WHEN EXTRACT(YEAR_MONTH FROM registerDate) = " . $db->quote(date('Ym', $date)) . " THEN 1 ELSE 0 END ) AS " . $db->quote(date('m-Y', $date));
    }

    // Select the required fields from the table.
    // Initialise the query.

    $query->select('
      a.where_heard
      ' . $select);
    $query->from('#__user_profile_fc as a');
    $query->join('left', '#__users b on a.user_id = b.id');
    $query->where('b.registerDate >=' . $db->quote(JHtml::_('date', '-6 MONTHS', 'Y-m-d')));
    $query->group('where_heard WITH ROLLUP');
        
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
