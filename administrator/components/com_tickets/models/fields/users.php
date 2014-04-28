<?php

/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports an HTML select list of categories
 */
class JFormFieldUsers extends JFormFieldList {

  /**
   * The form field type.
   *
   * @var		string
   * @since	1.6
   */
  protected $type = 'user';

  /**
   * Method to get the field input markup.
   *
   * @return	string	The field input markup.
   * @since	1.6
   */
  protected function getOptions() {
    // Initialize variables.
    $options = array();
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);

    $query->select('a.created_by as id, b.name as title');
    $query->from('#__tickets AS a');
    $query->join('left', '#__users b on b.id = a.created_by');
    $query->group('a.created_by');
    $query->order('b.name ASC');

    $db->setQuery($query);

    $items = $db->loadObjectList();

    // Check for a database error.
    if ($db->getErrorNum()) {
      JError::raiseWarning(500, $db->getErrorMsg());
    }
    // Loop over each subtree item
    foreach ($items as &$item) {
      $options[] = JHtml::_('select.option', $item->id, $item->title);
    }

    // Merge any additional options in the XML definition.
    $options = array_merge(parent::getOptions(), $options);
    return $options;
  }

}