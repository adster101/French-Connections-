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
  public static function addNote($property_id) {
    $title = JText::_('COM_HELLOWORLD_ADD_NOTE');

    return '<a href="' . JRoute::_('index.php?option=com_helloworld&task=note.add&property_id=' . (int) $property_id) . '">'
            . '<span class="label label-info"><i class="icon-plus"></i>' . $title . '</span></a>';
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
            . ' rel="{handler: \'iframe\', size: {x: 800, y: 550}}">'
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
  public static function stats($id, $u_id) {

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
  public static function reviewStates($checked_out = 0) {
        
    
    
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
            'active_title' => '',
            'inactive_title' => 'COM_HELLOWORLD_PROPERTY_NON_SUBMITTED',
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
            'active_class' => 'unpublish',
            'inactive_class' => 'unpublish'
        ),
    );
    return $states;
  }

  /**
   * @param	int $days	The number of days until the property expires, or null if a new sign up
   * @param	int $i
   */
  public static function renewalButton($days = '', $id = '', $review = 0, $canReview = false) {

    // Array of image, task, title, action.
    // Possible renewal states are
    // Expired (renew now)
    // About to expire (non auto-renew) (renew now) (opt in)
    // About to expire (auto renew) (opt out)
    // Publshed with > 28 days to renewal (non auto renew) (opt in)

    $value = '';
    $html = '';
    $allowEdit = true;

    if ($days <= 7 && $days >= 0 && !empty($days)) { // Property about to expire
      $value = 0;
    } elseif ($days < 0 && !empty($days)) { // Property has expired
      $value = 1;
    } elseif (empty($days)) { // A new sign up which has never been published...
      $value = 2;
    } elseif ($days > 7) { // 7 days or more until renewal
      $value = 3;
    } 

    if ($review == 2 && $canReview === false) {
      $value = 4;
      $allowEdit = false;
    }

    $states = array(
        0 => array(
            'chevron-right',
            'renewal.summary',
            'COM_HELLOWORLD_HELLOWORLD_RENEW_NOW_ABOUT_TO_EXPIRE',
            'COM_HELLOWORLD_HELLOWORLD_RENEW_NOW_BUTTON',
            'COM_HELLOWORLD_HELLOWORLD_RENEW_NOW_ABOUT_TO_EXPIRE_TOOLTIP',
            'btn-danger'),
        1 => array(
            'chevron-right',
            'renewal.summary',
            'COM_HELLOWORLD_HELLOWORLD_RENEW_NOW',
            'COM_HELLOWORLD_HELLOWORLD_RENEW_NOW_BUTTON',
            'COM_HELLOWORLD_HELLOWORLD_EDIT_LISTING_BUTTON_TOOLTIP',
            'btn-danger'),
        2 => array(
            'chevron-right',
            'listing.view',
            'COM_HELLOWORLD_HELLOWORLD_EDIT_LISTING',
            'COM_HELLOWORLD_HELLOWORLD_EDIT_LISTING_BUTTON',
            'COM_HELLOWORLD_HELLOWORLD_EDIT_LISTING_BUTTON_TOOLTIP',
            'btn-primary'),
        3 => array(
            'chevron-right',
            'listing.view',
            'COM_HELLOWORLD_HELLOWORLD_EDIT_LISTING',
            'COM_HELLOWORLD_HELLOWORLD_EDIT_LISTING_BUTTON',
            'COM_HELLOWORLD_HELLOWORLD_EDIT_LISTING_BUTTON_TOOLTIP',
            'btn-primary'),
        4 => array(
            'locked',
            'listing.view',
            'COM_HELLOWORLD_HELLOWORLD_EDIT_LISTING',
            'COM_HELLOWORLD_HELLOWORLD_EDIT_LISTING_BUTTON',
            'COM_HELLOWORLD_HELLOWORLD_EDIT_LISTING_LOCKED_BUTTON_TOOLTIP',
            'btn-primary disabled')
    );

    $state = JArrayHelper::getValue($states, (int) $value, $states[2]);
    if ($allowEdit) {
      $html .= '<a rel="tooltip" class="btn ' . $state[5] . '" href="' . JRoute::_('index.php?option=com_helloworld&task=' . $state[1] . '&id=' . (int) $id) . '" title="' . JText::_($state[4]) . '">';
      
    } else {
      $html .= '<span rel="tooltip" class="btn ' . $state[5] . '" title="' . JText::_($state[4]) . '">';

    }
    $html .= '<i class=\'icon-' . $state[0] . '\'></i>&nbsp;';
    $html .= JText::_($state[3]);
    
    $html.= ($allowEdit) ? '</a>' : '</span>';

    return $html;
  }

  /**
   * @param	int $value	The state value
   * @param	int $i
   */
  public static function autorenewalstate($value = '', $id = '') {

    $html = '';

    $states = array(
        0 => array(
            'chevron-right',
            'autorenewals.showtransactionlist',
            'COM_HELLOWORLD_HELLOWORLD_ENABLE_AUTO_RENEWALS',
            'COM_HELLOWORLD_HELLOWORLD_ENABLE_AUTO_RENEWALS_BUTTON',
            'COM_HELLOWORLD_HELLOWORLD_ENABLE_AUTO_RENEWALS_CLICK_HERE'
        ),
        1 => array(
            'chevron-right',
            'autorenewals.showtransactionlist',
            'COM_HELLOWORLD_HELLOWORLD_CANCEL_AUTO_RENEWALS',
            'COM_HELLOWORLD_HELLOWORLD_CANCEL_AUTO_RENEWALS_BUTTON',
            'COM_HELLOWORLD_HELLOWORLD_CANCEL_AUTO_RENEWALS_CLICK_HERE')
    );

    $state = JArrayHelper::getValue($states, (int) $value, $states[0]);

    $html .= '<a rel="tooltip" class="" href="' . JRoute::_('index.php?option=com_helloworld&task=' . $state[1] . '&id=' . (int) $id) . '" title="' . JText::_($state[4]) . '">';
    $html .= JText::_($state[3]);
    $html .= '<i class=\'icon-' . $state[0] . '\'></i>';
    $html .='</a>';

    return $html;
  }

  /*
   * A generic make button function button
   *
   *
   */

  public static function button($btnClass = '', $task = '', $iconClass = '', $text) {

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
  public static function progressButton($listing_id = '', $unit_id = '', $controller = '', $action = 'edit', $icon = '', $button_text = '', $item = '', $urlParam = 'property_id', $btnClass = '') {
    $active = false;
    $progress_icon = 'warning';
    $okay_icon = 'ok';

    $html = '';

    if (!empty($listing_id) && ($controller == 'propertyversions')) {

      $active = true;
      $progress_icon = $okay_icon;
      $id = $listing_id;
    } elseif (empty($listing_id) && ($controller == 'propertyversions')) {

      $active = true;
      $progress_icon = $progress_icon;
      $id = $listing_id;

    } elseif (empty($unit_id) && $controller == 'unitversions' && !empty($listing_id) && $action == 'edit') { // This property has no unit, or unit details not completed...
      $active = true;
      $progress_icon = $progress_icon;
      $id = $listing_id;
      // Set urlParam here as a new unit may need listing id in GET scope
      $urlParam = 'property_id';

    } elseif (!empty($unit_id) && $controller == 'images') {

      $active = true;
      $progress_icon = ($item->images > 0) ? $okay_icon : $progress_icon;
      $id = $unit_id;
    } elseif (!empty($unit_id) && $controller == 'availability') {

      $progress_icon = ($item->availability > 0) ? $okay_icon : $progress_icon;
      $active = true;
      $id = $unit_id;
    } elseif (!empty($unit_id) && $controller == 'unitversions' && !empty($listing_id) && $action == 'tariffs') {

      $active = true;
      $progress_icon = ($item->tariffs > 0) ? $okay_icon : $progress_icon;
      $id = $unit_id;
    } elseif (!empty($unit_id) && $controller == 'unitversions') {

      $progress_icon = ($action == 'reviews') ? '' : $okay_icon;

      $active = true;
      $id = $unit_id;
    } elseif (!empty($unit_id) && $controller == 'unitversions') {

      $active = true;
      $progress_icon = '';
      $id = $unit_id;
    }

    if ($active) {
      // This button should be active
      $html .= '<a class="' . $btnClass . '"'
              . ' href="' . JRoute::_('index.php?option=com_helloworld&task=' . $controller . '.' . $action . '&' . $urlParam . '=' . (int) $id . '&' . JSession::getFormToken() . '=1') . '"'
              . ' rel="tooltip">';
      $html .= '<i class="icon icon-' . $icon . '"></i>';
      $html .= '&nbsp;' . Jtext::_($button_text);
      if (!empty($progress_icon)) {
        $html .= '&nbsp;<i class="icon icon-' . $progress_icon . '"></i>';
      }
      $html .= '</a>';
    } else {
      // This button should be inactive
      $html .= '<span disabled class="' . $btnClass . '"'
              . ' rel="tooltip">';
      $html .= '<i class="icon icon-' . $icon . '"></i>';
      $html .= '&nbsp;' . Jtext::_($button_text);
      if (!empty($progress_icon)) {
        $html .= '&nbsp;<i class="icon icon-' . $progress_icon . '"></i>';
      }
      $html .= '</span>';
    }



    return $html;
  }

}
