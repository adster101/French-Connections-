<?php

/**
 * @package     Joomla.Libraries
 * @subpackage  Toolbar
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('JPATH_PLATFORM') or die;

/**
 * Renders a standard button
 *
 * @package     Joomla.Libraries
 * @subpackage  Toolbar
 * @since       3.0
 */
class JToolbarButtonPreview extends JToolbarButton
{

  /**
   * Button type
   *
   * @var    string
   */
  protected $_name = 'Preview';

  /**
   * Fetch the HTML for the button
   *
   * @param   string   $type  Unused string.
   * @param   string   $text  Button text.
   * @param   string   $task  Task associated with the button.
   * @param   boolean  $list  True to allow lists
   *
   * @return  string  HTML string for the button
   *
   * @since   3.0
   */
  public function fetchButton($type = 'Preview', $name = '', $text = '', $property_id = '', $unit_id = '', $option = 'com_accommodation')
  {

    // Store all data to the options array for use with JLayout
    $options = array();
    $options['text'] = JText::_($text);
    $options['class'] = $this->fetchIconClass($name);
    $options['property_id'] = $property_id;
    $options['unit_id'] = $unit_id;

    // Below is a bit of a fudge which basically gets the alias for $option.
    // Then just manually build the URL for the preview link.
    // TO DO - Move this to a helper function called in the view and pass the url
    // as an argument.
    $app = JApplication::getInstance('site');
    $menu = $app->getMenu();
    $items = $menu->getItems('component', $option);
    
    // If there is a unit ID append it to the URL, not really necessary but avoids a 301
    $unit_append = ($unit_id) ? '?unit_id=' . (int) $unit_id . '&preview=1' : '?preview=1';
    
    // Add the url to the options for rendering in the layout
    $options['url'] = '/' . $items[0]->alias . '/' . (int) $property_id . $unit_append;

    // Instantiate a new JLayoutFile instance and render the layout
    $layout = new JLayoutFile('frenchconnections.property.admin');

    return $layout->render($options);
  }

  /**
   * Get the button CSS Id
   *
   * @param   string   $type      Unused string.
   * @param   string   $name      Name to be used as apart of the id
   * @param   string   $text      Button text
   * @param   string   $task      The task associated with the button
   * @param   boolean  $list      True to allow use of lists
   * @param   boolean  $hideMenu  True to hide the menu on click
   *
   * @return  string  Button CSS Id
   *
   * @since   3.0
   */
  public function fetchId($type = 'Snooze', $name = '', $text = '', $task = '', $list = true, $hideMenu = false)
  {
    return $this->_parent->getName() . '-' . $name;
  }

}
