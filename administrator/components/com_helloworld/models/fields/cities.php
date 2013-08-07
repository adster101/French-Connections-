<?php

/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @since		1.6
 */
class JFormFieldCities extends JFormFieldList {

  /**
   * The form field type.
   *
   * @var		string
   * @since	1.6
   */
  protected $type = 'Cities';

  /**
   * Method to get the field options.
   *
   * @return	array	The field option objects.
   * @since	1.6
   */
  protected function getOptions() {
    // Filter out any regions, areas etc
    $options = array();
    // Get latitude
    $latitude = $this->element['latitude'] ? $this->element['latitude'] : '';
    $longitude = $this->element['longitude'] ? $this->element['longitude'] : '';

    if (!empty($latitude) && !empty($longitude)) {


      // Initialize variables.
      $options = array();
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $query->select('id, title, level');
      $query->select(
              '(
        3959 * acos( cos( radians(' . $longitude . ') )
        * cos( radians( latitude ) )
        * cos( radians( longitude ) -
        radians(' . $latitude . ') ) +
        sin( radians(' . $longitude . ') )
        * sin( radians( latitude ) ) ) )
        AS distance
            ');
      $query->from('#__classifications');
      $query->where('level = 5');

      $query->having('distance < 50');
      $query->order('distance');
      $db->setQuery($query, 0, 10);
      $items = $db->loadObjectList();

      // Check for a database error.
      if ($db->getErrorNum()) {
        JError::raiseWarning(500, $db->getErrorMsg());
      }

      // Loop over each subtree item
      foreach ($items as &$item) {
        $repeat = ($item->level - 1 >= 0) ? $item->level - 1 : 0;
        $item->title = $item->title . ' - ' . round($item->distance, 0) . ' Miles';
        $options[] = JHtml::_('select.option', $item->id, $item->title);
      }
    }





    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), $options);
    return $options;
  }

}
