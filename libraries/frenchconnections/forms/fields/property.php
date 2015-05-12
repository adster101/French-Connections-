<?php

/**
 * @package     Joomla.Libraries
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field to load a list of content authors
 *
 * @package     Joomla.Libraries
 * @subpackage  Form
 * @since       3.2
 */
class JFormFieldProperty extends JFormFieldList {

  /**
   * The form field type.
   *
   * @var    string
   * @since  3.2
   */
  public $type = 'Property';

  /**
   * Cached array of the category items.
   *
   * @var    array
   * @since  3.2
   */

  /**
   * Method to get the options to populate list
   *
   * @return  array  The field option objects.
   *
   * @since   3.2
   */
  protected function getOptions() {
    // Accepted modifiers
    $hash = md5($this->element);

    $user = JFactory::getUser();


    $options = array();

    $db = JFactory::getDbo();

    // Construct the query which gets the rental property IDs
    $query = $db->getQuery(true)
            ->select('a.id AS value, a.id AS title')
            ->from('#__property AS a')
            ->where('a.created_by = ' . (int) $user->id);

    // Construct the query which gets the rental property IDs
    $query2 = $db->getQuery(true)
            ->select('a.id AS value, a.id AS title')
            ->from('#__realestate_property AS a')
            ->where('a.created_by = ' . (int) $user->id);          
    
    $query->union($query2);
    
    // Setup the query
    $db->setQuery($query);
    $items = $db->loadObjectList();
    
    $options = array();

    // Check for a database error.
    if ($db->getErrorNum()) {
      JError::raiseWarning(500, $db->getErrorMsg());
    }
    
    // Loop over each subtree item
    foreach ($items as &$item) {
      $options[] = JHtml::_('select.option', $item->value, $item->title);
    }

    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), $options);
    return $options;
  }

}
