<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Provides input for choosing a country
 *
 * @package     Joomla.Plugin
 * @subpackage  User.profile
 * @since       2.5.5
 */
class JFormFieldState extends JFormFieldList {

  /**
   * The form field type.
   *
   * @var    string
   * @since  2.5.5
   */
  public $type = 'State';


  /**
   * Method to get the field options.
   *
   * @return  array  The field option objects.
   *
   * @since   11.1
   */
  protected function getOptions() {

    $options = array();
    
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('a.abbrev as id, a.name as name');
    $query->from('#__states AS a');
    $query->order('a.id asc');

    $db->setQuery($query);
    $items = $db->loadObjectList();

    // Check for a database error.
    if ($db->getErrorNum()) {
      JError::raiseWarning(500, $db->getErrorMsg());
    }
    // Loop over each subtree item
    foreach ($items as &$item) {
      $options[] = JHtml::_('select.option', $item->id, $item->name);
    }

    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), $options);
    return $options;
  }

}
