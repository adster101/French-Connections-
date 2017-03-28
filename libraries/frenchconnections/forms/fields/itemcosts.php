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
class JFormFieldItemcosts extends JFormFieldList
{

  /**
   * The form field type.
   *
   * @var		string
   * @since	1.6
   */
  protected $type = 'Itemcosts';

  /**
   * Method to get the field options.
   *
   * @return	array	The field option objects.
   * @since	1.6
   */
  protected function getOptions()
  {
    // Filter out any regions, areas etc
    $cat = $this->element['cat'] ? $this->element['cat'] : '';

    // Initialize variables. // Cache the db result here so it can be reused - or rely on sql cache?
    $options = array();

    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('code as value, concat("[", code, "] ", description) as text');
    $query->from('#__item_costs');
    $query->where('catid = ' . (int) $cat);


    $db->setQuery($query);
    $items = $db->loadObjectList();

    // Check for a database error.
    if ($db->getErrorNum())
    {
      JError::raiseWarning(500, $db->getErrorMsg());
    }

    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), $items);
    return $options;
  }

}
