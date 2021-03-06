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
class JToolbarButtonSnooze extends JToolbarButton
{

  /**
   * Button type
   *
   * @var    string
   */
  protected $_name = 'Snooze';

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
  public function fetchButton($type = 'Snooze', $text = '', $remote = '', $id = '', $icon = '')
  {

    // Store all data to the options array for use with JLayout
    $options = array();
    $options['title'] = JText::_($text);
    $options['doTask'] = $this->_getCommand();
    $options['remote'] = $remote;
    $options['id'] = $id;
    $options['icon'] = $icon;

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

  /**
   * Get the JavaScript command for the button
   *
   * @param   string   $name  The task name as seen by the user
   * @param   string   $task  The task used by the application
   * @param   boolean  $list  True is requires a list confirmation.
   *
   * @return  string   JavaScript command string
   *
   * @since   3.0
   */
  protected function _getCommand()
  {
    JHtml::_('behavior.framework');
    $message = JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
    $message = addslashes($message);

    $cmd = "if (document.adminForm.boxchecked.value==0){alert('$message');return false}";



    return $cmd;
  }

}
