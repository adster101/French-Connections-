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

  protected function getInput() {

    // Get the field options.
    $cities = (array) $this->getOptions();
    $data = array();
    
		$class = !empty($this->class) ? $this->class : '';

    foreach ($cities as $city) {
      $data[] = array(
          'value' => $city->id,
          'text' => $city->title,
          'attr' => array('data-latitude' => $city->latitude, 'data-longitude'=>$city->longitude)
      );
    }

    $options = array(
        'id' => $this->id, // HTML id for select field
        'list.attr' => array(// additional HTML attributes for select field
            'class' => $class,
        ),
        'list.translate' => false, // true to translate
        'option.key' => 'value', // key name for value in data array
        'option.text' => 'text', // key name for text in data array
        'option.attr' => 'attr', // key name for attr in data array
        'list.select' => $this->value, // value of the SELECTED field
    );

    $result = JHtmlSelect::genericlist($data, $this->name, $options);
    return $result;
  }

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
    $dept = $this->element['department'] ? $this->element['department'] : '';
    
    $items = array();
    
    if (!empty($dept)) {

      // Initialize variables. // Cache the db result here so it can be reused - or rely on sql cache?
      $options = array();
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $query->select('id, title, latitude, longitude');
      $query->from('#__classifications');
      $query->where('parent_id = ' . (int) $dept);

      $query->order('title', 'asc');
      $db->setQuery($query);
      $items = $db->loadObjectList();

      // Check for a database error.
      if ($db->getErrorNum()) {
        JError::raiseWarning(500, $db->getErrorMsg());
      }
    }
    
    return $items;
  }

}
