<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Extended Utility class for the Users component.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       2.5
 */
class JHtmlProperty {

  /**
   * Display an image.
   *
   * @param   string  $src  The source of the image
   *
   * @return  string  A <img> element if the specified file exists, otherwise, a null string
   *
   * @since   2.5
   */
  public static function image($src) {
    $src = preg_replace('#[^A-Z0-9\-_\./]#i', '', $src);
    $file = JPATH_SITE . '/' . $src;

    jimport('joomla.filesystem.path');
    JPath::check($file);

    if (!file_exists($file)) {
      return '';
    }

    return '<img src="' . JUri::root() . $src . '" alt="" />';
  }

  /**
   * Displays an icon to add a note for this user.
   *
   * @param   integer  $userId  The user ID
   *
   * @return  string  A link to add a note
   *
   * @since   2.5
   */
  public static function addNote($userId) {
    $title = JText::_('COM_USERS_ADD_NOTE');

    return '<a href="' . JRoute::_('index.php?option=com_users&task=note.add&u_id=' . (int) $userId) . '">'
            . '<span class="label label-info"><i class="icon-vcard"></i>' . $title . '</span></a>';
  }

  /**
   * Displays an icon to filter the notes list on this user.
   *
   * @param   integer  $count   The number of notes for the user
   * @param   integer  $userId  The user ID
   *
   * @return  string  A link to apply a filter
   *
   * @since   2.5
   */
  public static function filterNotes($count, $userId) {
    if (empty($count)) {
      return '';
    }

    $title = JText::_('COM_USERS_FILTER_NOTES');

    return '<a href="' . JRoute::_('index.php?option=com_users&view=notes&filter_search=uid:' . (int) $userId) . '">'
            . JHtml::_('image', 'admin/filter_16.png', 'COM_USERS_NOTES', array('title' => $title), true) . '</a>';
  }

  /**
   * Displays a note icon.
   *
   * @param   integer  $userId  The property ID
   *
   * @return  string  A link to a modal window with the user notes
   *
   * @since   2.5
   */
  public static function notes($id) {
    if (empty($id)) {
      return '';
    }

    $title = JText::_('COM_HELLOWORLD_HELLOWORLD_VIEW_PROPERTY_NOTES');

    return '<a class="modal"'
            . ' href="' . JRoute::_('index.php?option=com_helloworld&view=notes&tmpl=component&layout=modal&property_id=' . (int) $id) . '"'
            . ' rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'
            . '<span class="label label-info"><i class="icon-drawer-2"></i>' . $title . '</span></a>';
  }

  /**
   * Displays a 'my stats' icon
   *
   * @param   integer  $userId  The property ID
   *
   * @return  string  A link to a modal window with the user notes
   *
   * @since   2.5
   */
  public static function stats($id,$u_id) {

    if (empty($id)) {
      return '';
    }

    $title = JText::_('COM_HELLOWORLD_HELLOWORLD_VIEW_PROPERTY_STATS');

    return '<a class="modal"'
            . ' href="' . JRoute::_('index.php?option=com_helloworld&task=listing.stats&tmpl=component&layout=modal&id=' . (int) $id . '&u_id=' . (int) $u_id . '&' . JSession::getFormToken() . '=1') . '"'
            . ' rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'
            . '<span class="label label-info"><i class="icon-bars"></i>' . $title . '</span></a>';
  }


  /**
   * Build an array of activate states to be used by jgrid.state,
   *
   * @return  array  a list of possible states to display
   *
   * @since  3.0
   */
  public static function reviewStates() {
    $states = array(
        -1 => array(
            'task' => '',
            'text' => '',
            'active_title' => '',
            'inactive_title' => 'COM_USERS_ACTIVATED',
            'tip' => true,
            'active_class' => 'publish',
            'inactive_class' => 'publish'
        ),
        0 => array(
            'task' => '',
            'text' => '',
            'active_title' => '',
            'inactive_title' => 'COM_USERS_ACTIVATED',
            'tip' => true,
            'active_class' => 'publish',
            'inactive_class' => 'publish'
        ),
        1 => array(
            'task' => 'submit',
            'text' => '',
            'active_title' => 'COM_PROPERTY_TOOLBAR_ACTIVATE',
            'inactive_title' => '',
            'tip' => true,
            'active_class' => 'warning',
            'inactive_class' => 'warning'
        ),
        2 => array(
            'task' => 'review',
            'text' => '',
            'active_title' => 'COM_HELLOWORLD_PROPERTY_LOCKED_FOR_EDITING',
            'inactive_title' => 'COM_HELLOWORLD_PROPERTY_LOCKED_FOR_EDITING',
            'tip' => true,
            'active_class' => 'locked',
            'inactive_class' => 'locked'
        ),
    );
    return $states;
  }

  /**
   * @param	int $value	The state value
   * @param	int $i
   */
  public static function renew($i, $title) {
    $html = '';


    $html = '<a rel="tooltip" href="javascript::void(0);" onclick="return listItemTask(\'cb' . $i . '\',\'renewal.summary\')" title="' . $title . '" class="btn btn-danger">'
            . JText::_('COM_HELLOWORLD_HELLOWORLD_RENEW_NOW') . '</a>';

    return $html;
  }

  /*
   * A generic make button function button
   *
   *
   */
  public static function button ($btnClass = '', $task = '', $iconClass = '', $text) {

    $html = '';
    $html.='<button class="' . $btnClass . '" onclick="Joomla.submitbutton(\'' . $task . '\')">'
      . JText::_($text)
      . '<i class="' . $iconClass . '"></i>'
      . '</button>';

    return $html;

  }


  /**
   * Gets a list of the actions that can be performed.
   *
   * @param   string	$extension	The extension.
   * @param   integer  $categoryId	The category ID.
   *
   * @return  JObject
   * @since   1.6
   */
  public static function getActions($extension, $categoryId = 0) {
    $user = JFactory::getUser();
    $result = new JObject;
    $parts = explode('.', $extension);
    $component = $parts[0];

    if (empty($categoryId)) {
      $assetName = $component;
      $level = 'component';
    } else {
      $assetName = $component . '.category.' . (int) $categoryId;
      $level = 'category';
    }

    $actions = JAccess::getActions($component, $level);

    foreach ($actions as $action) {
      $result->set($action->name, $user->authorise($action->name, $assetName));
    }

    return $result;
  }

  /**
   * @param	int $value
   * @param	int $i
   */
  public static function progressButton($listing_id = '', $unit_id = '', $controller = '', $action = 'edit', $icon = '', $button_text = '', $item = '', $urlParam = 'parent_id',$btnClass = '') {
    $active = false;
    $progress_icon = 'warning';
    $html = '';

    if (!empty($listing_id) && ($controller == 'propertyversions')) {

      $active = true;
      $progress_icon = 'ok';
      $id = $listing_id;

    } elseif (empty($listing_id) && ($controller == 'propertyversions')) {

      $active = true;
      $progress_icon = 'warning';
      $id = $listing_id;

    }  elseif (empty($unit_id) && $controller == 'unitversions' && !empty($listing_id)) { // This property has no unit, or unit details not completed...

      $active = true;
      $progress_icon = 'warning';
      $id = $listing_id;
      // Set urlParam here as a new unit may need listing id in GET scope
      $urlParam = 'parent_id';

    } elseif (!empty($unit_id) && $controller == 'images') {

      $active = true;
      $progress_icon = ($item->images > 0) ? 'ok' : 'warning';
      $id = $unit_id;

    } elseif (!empty($unit_id) && $controller == 'availability') {

      $progress_icon = ($item->availability > 0) ? 'ok' : 'warning';
      $active = true;
      $id = $unit_id;

    } elseif (!empty($unit_id) && $controller == 'tariffs') {

      $active = true;
      $progress_icon = ($item->tariffs > 0) ? 'ok' : 'warning';
      $id = $unit_id;

    } elseif (!empty($unit_id) && $controller == 'unitversions') {

      $active = true;
      $progress_icon = 'ok';
      $id = $unit_id;

    }

    if ($active) {
      // This button should be active
      $html .= '<a class="' . $btnClass . '"'
              . ' href="' . JRoute::_('index.php?option=com_helloworld&task=' . $controller . '.' . $action . '&' . $urlParam . '=' . (int) $id . '&' . JSession::getFormToken() . '=1') . '"'
              . ' rel="tooltip">';
      $html .= '<i class="icon icon-' . $icon . '"></i>';
      $html .= '&nbsp;' . Jtext::_($button_text);
      $html .= '&nbsp;<i class="icon icon-' . $progress_icon . '"></i>';
      $html .= '</a>';
    } else {
      // This button should be inactive
      $html .= '<span disabled class="' . $btnClass . '"'
              . ' rel="tooltip">';
      $html .= '<i class="icon icon-' . $icon . '"></i>';
      $html .= '&nbsp;' . Jtext::_($button_text);
      $html .= '&nbsp;<i class="icon icon-' . $progress_icon . '"></i>';
      $html .= '</span>';
    }



    return $html;
  }

}
