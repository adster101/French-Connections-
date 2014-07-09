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
class InvoicesModelDownloadUserCards extends JModelList
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

    // Select the required fields from the table.
    $query->select(
            'a.track_date as track_date,'
            . 'a.track_type as track_type,'
            . $db->quoteName('a.count') . ' as ' . $db->quoteName('count')
    );
    $query->from($db->quoteName('#__banner_tracks') . ' AS a');

    // Join with the banners
    $query->join('LEFT', $db->quoteName('#__banners') . ' as b ON b.id=a.banner_id')
            ->select('b.name as name');



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
      $this->content = '';
      $this->content .=
              JText::_('COM_BANNERS_HEADING_NAME') . "\t" .
              JText::_('COM_BANNERS_HEADING_CLIENT') . "\t" .
              JText::_('JCATEGORY') . "\t" .
              JText::_('COM_BANNERS_HEADING_TYPE') . "\t" .
              JText::_('COM_BANNERS_HEADING_COUNT') . "\t" .
              JText::_('JDATE') . '"' . "\r\n";

      foreach ($this->getItems() as $item)
      {
        $this->content .=
                '"' . str_replace('"', '""', $item->name) . '"\t"' .
                str_replace('"', '""', $item->client_name) . '"\t"' .
                str_replace('"', '""', $item->category_title) . '"\t"' .
                str_replace('"', '""', ($item->track_type == 1 ? JText::_('COM_BANNERS_IMPRESSION') : JText::_('COM_BANNERS_CLICK'))) . '"\t"' .
                str_replace('"', '""', $item->count) . '"\t"' .
                str_replace('"', '""', $item->track_date) . '"' . "\n";
      }
    }
    
    return $this->content;
  }

}