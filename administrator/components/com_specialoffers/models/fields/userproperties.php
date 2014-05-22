<?php

/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class JFormFieldUserProperties extends JFormFieldList
{

  /**
   * UserProperties fields - a list of properties
   *
   * @var		string
   * @since	1.6
   */
  public $type = 'UserProperties';

  /**
   * Based on the created_by field of the property being edited we pull out a list of other properties
   * owned/created by this user.
   * TO DO - This needs to be optgrouped on the PROPERTY ID
   * @return	array	The field option objects.
   * @since	1.6
   */
  protected function getOptions()
  {
    $created_by = '';

    // Initialise variables.
    $options = array();

    $db = JFactory::getDbo();  // Get the database instance

    $query = $db->getQuery(true);

    $user = JFactory::getUser(); // Get current logged in user

    $query->select('a.id, b.unit_title');
    $query->from('#__unit AS a');
    $query->join('left', '#__unit_versions b on (a.id = b.unit_id and b.id = (select max(c.id) from #__unit_versions c where unit_id = a.id))');
    $query->join('left', '#__property d on d.id = b.property_id');
    $query->where('d.created_by = ' . $user->id);  // Select only the props created by the user that created this property
    
    // Get the options.
    $db->setQuery($query);

    $properties = $db->loadObjectList();
    // Loop over each subtree item
    $options[] = JHtml::_('select.option', '', JText::_('COM_SPECIALOFFERS_CHOOSE_PROPERTY'));

    foreach ($properties as $property)
    {
      $options[] = JHtml::_('select.option', $property->id, $property->unit_title);
    }


    $options = array_merge(parent::getOptions(), $options);

    return $options;
  }

}

