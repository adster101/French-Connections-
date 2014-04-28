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
    $title = JText::_('COM_RENTAL_ADD_NOTE');

    return '<a href="' . JRoute::_('index.php?option=com_rental&task=note.add&property_id=' . (int) $property_id) . '">'
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

    $title = JText::_('COM_RENTAL_HELLOWORLD_VIEW_PROPERTY_NOTES');

    return '<a class="modal"'
            . ' href="' . JRoute::_('index.php?option=com_rental&view=notes&tmpl=component&layout=modal&property_id=' . (int) $id) . '"'
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

    $title = JText::_('COM_RENTAL_HELLOWORLD_VIEW_PROPERTY_STATS');

    return '<a class="modal"'
            . ' href="' . JRoute::_('index.php?option=com_stats&tmpl=component&layout=modal&id=' . (int) $id . '&u_id=' . (int) $u_id . '&' . JSession::getFormToken() . '=1') . '"'
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
            'inactive_title' => 'COM_RENTAL_PROPERTY_NON_SUBMITTED',
            'tip' => true,
            'active_class' => 'warning',
            'inactive_class' => 'warning'
        ),
        2 => array(
            'task' => 'review',
            'text' => '',
            'active_title' => 'COM_RENTAL_PROPERTY_LOCKED_FOR_EDITING',
            'inactive_title' => 'COM_RENTAL_PROPERTY_LOCKED_FOR_EDITING',
            'tip' => true,
            'active_class' => 'unpublish',
            'inactive_class' => 'unpublish'
        ),
    );
    return $states;
  }

  /**
   * Generates an button group for editing a property
   * 
   * @param type $days
   * @param type $id
   * @param type $unit_id
   */
  public static function editButton($days = '', $id = '', $unit_id = '', $review = '') {
    // Array of image, task, title, action.
    // Possible renewal states are
    // Expired (renew now)
    // About to expire (non auto-renew) (renew now) (opt in)
    // About to expire (auto renew) (opt out)
    // Publshed with > 28 days to renewal (non auto renew) (opt in)

    $value = '';
    $html = '';
    $allowEdit = true;

    if (empty($days) || $days > 28) { // A new sign up which has never been published...
      $value = 2;
    } elseif ($days <= 7 && $days >= 0) { // Property about to expire
      $value = 1;
    } elseif ($days < 0 && !empty($days)) { // Property has expired
      $value = 1;
    } elseif ($days >= 7 && $days <= 28) { // More than seven days but expiring within the month.
      $value = 3;
    }

    if ($review == 2) {
      $value = 4;
      $allowEdit = false;
    }

    $states = array(
        0 => array(
            'COM_RENTAL_HELLOWORLD_RENEW_NOW_ABOUT_TO_EXPIRE',
            'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON',
            'COM_RENTAL_HELLOWORLD_RENEW_NOW_ABOUT_TO_EXPIRE_TOOLTIP',
            'btn-primary'),
        1 => array(
            'COM_RENTAL_HELLOWORLD_RENEW_NOW',
            'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON',
            'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON_TOOLTIP',
            'btn-primary'),
        2 => array(
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON_TOOLTIP',
            'btn-primary'),
        3 => array(
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON_DUE_FOR_RENEWAL_TOOLTIP',
            'btn-primary'),
        4 => array(
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING_LOCKED_BUTTON_TOOLTIP',
            'btn-primary disabled')
    );

    $state = JArrayHelper::getValue($states, (int) $value, $states[2]);
    $html .= '<div class="btn-group">';
    if ($allowEdit) {
      $html .= '<a class="btn ' . $state[3] . '" href="' . JRoute::_('index.php?option=com_rental&task=listing.view&id=' . (int) $id . '&' . JSession::getFormToken() . '=1') . '">';
      $html .= JText::sprintf($state[0], (int) $id);
      $html .= '</a>';
      $html .= '<button class="btn dropdown-toggle ' . $state[3] . '" data-toggle="dropdown">';
      $html .= '<span class="caret"></span>';
      $html .= '</button>';
      $html .= '<ul class="dropdown-menu">';
      $html .= '<li>';
      $html .= '<a href="' . JRoute::_('index.php?option=com_rental&task=propertyversions.edit&property_id=' . (int) $id . '&' . JSession::getFormToken() . '=1') . '">';
      $html .= '<i class="icon icon-location">&nbsp;</i>&nbsp;';
      $html .= JText::_('COM_RENTAL_SUBMENU_LOCATION');
      $html .= '</a>';
      $html .= '</li>';
      $html .= '<li>';
      $html .= '<a href="' . JRoute::_('index.php?option=com_rental&task=unitversions.edit&unit_id=' . (int) $unit_id . '&' . JSession::getFormToken() . '=1') . '">';
      $html .= '<i class="icon icon-home">&nbsp;</i>&nbsp;';
      $html .= JText::_('COM_RENTAL_SUBMENU_PROPERTY');
      $html .= '</a>';
      $html .= '</li>';
      $html .= '<li>';
      $html .= '<a href="' . JRoute::_('index.php?option=com_rental&task=images.manage&unit_id=' . (int) $unit_id . '&' . JSession::getFormToken() . '=1') . '">';
      $html .= '<i class="icon icon-pictures">&nbsp;</i>&nbsp;';
      $html .= JText::_('IMAGE_GALLERY');
      $html .= '</a>';
      $html .= '</li>';
      $html .= '<li>';
      $html .= '<a href="' . JRoute::_('index.php?option=com_rental&task=availability.manage&unit_id=' . (int) $unit_id . '&' . JSession::getFormToken() . '=1') . '">';
      $html .= '<i class="icon icon-calendar">&nbsp;</i>&nbsp;';
      $html .= JText::_('COM_RENTAL_SUBMENU_PROPERTY');
      $html .= '</a>';
      $html .= '</li>';
      $html .= '<li>';
      $html .= '<a href="' . JRoute::_('index.php?option=com_rental&task=tariffs.edit&unit_id=' . (int) $unit_id . '&' . JSession::getFormToken() . '=1') . '">';
      $html .= '<i class="icon icon-briefcase">&nbsp;</i>&nbsp;';
      $html .= JText::_('COM_RENTAL_SUBMENU_MANAGE_TARIFFS');
      $html .= '</a>';
      $html .= '</li>';
      $html .= '<li>';
      $html .= '<a href="' . JRoute::_('index.php?option=com_rental&task=contactdetails.edit&property_id=' . (int) $id . '&' . JSession::getFormToken() . '=1') . '">';
      $html .= '<i class="icon icon-envelope">&nbsp;</i>&nbsp;';
      $html .= JText::_('COM_RENTAL_SUBMENU_MANAGE_CONTACT_DETAILS');
      $html .= '</a>';
      $html .= '</li>';
      $html .= '</ul>';
    } else {

      $html .= '<span rel="tooltip" class="btn ' . $state[3] . '" title="' . JText::_($state[2]) . '">';
      $html .= '<i class="icon icon-locked">&nbsp;</i>';
    }
    $html .= JText::_($state[2]);

    $html.= ($allowEdit) ? '</a>' : '</span>';
    $html .= '</div>';
    return $html;
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

    if (empty($days) || $days > 28) { // A new sign up which has never been published...
      $value = 2;
    } elseif ($days <= 7 && $days >= 0) { // Property about to expire
      $value = 0;
    } elseif ($days < 0 && !empty($days)) { // Property has expired
      $value = 1;
    } elseif ($days >= 7 && $days <= 28) { // More than seven days but expiring within the month.
      $value = 3;
    }

    if ($review == 2) {
      $value = 4;
      $allowEdit = false;
    }

    $states = array(
        0 => array(
            'chevron-right',
            'renewal.summary',
            'COM_RENTAL_HELLOWORLD_RENEW_NOW_ABOUT_TO_EXPIRE',
            'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON',
            'COM_RENTAL_HELLOWORLD_RENEW_NOW_ABOUT_TO_EXPIRE_TOOLTIP',
            'btn-warning'),
        1 => array(
            'chevron-right',
            'renewal.summary',
            'COM_RENTAL_HELLOWORLD_RENEW_NOW',
            'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON',
            'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON_TOOLTIP',
            'btn-danger'),
        2 => array(
            'chevron-right',
            'listing.view',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON_TOOLTIP',
            'btn-primary'),
        3 => array(
            'chevron-right',
            'renewal.summary',
            'COM_RENTAL_HELLOWORLD_RENEW_NOW',
            'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON_DUE_FOR_RENEWAL_TOOLTIP',
            'btn-info'),
        4 => array(
            'locked',
            'listing.view',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON',
            'COM_RENTAL_HELLOWORLD_EDIT_LISTING_LOCKED_BUTTON_TOOLTIP',
            'btn-primary disabled')
    );

    $state = JArrayHelper::getValue($states, (int) $value, $states[2]);
    if ($allowEdit) {
      $html .= '<a rel="tooltip" class="btn ' . $state[5] . '" href="' . JRoute::_('index.php?option=com_rental&task=' . $state[1] . '&id=' . (int) $id) . '" title="' . JText::_($state[4]) . '">';
    } else {
      $html .= '<span rel="tooltip" class="btn ' . $state[5] . '" title="' . JText::_($state[4]) . '">';
    }
    $html .= JText::_($state[3]);

    $html .= '&nbsp;<i class=\'icon-' . $state[0] . '\'>&nbsp;</i>';

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
            'COM_RENTAL_HELLOWORLD_ENABLE_AUTO_RENEWALS',
            'COM_RENTAL_HELLOWORLD_ENABLE_AUTO_RENEWALS_BUTTON',
            'COM_RENTAL_HELLOWORLD_ENABLE_AUTO_RENEWALS_CLICK_HERE'
        ),
        1 => array(
            'chevron-right',
            'autorenewals.showtransactionlist',
            'COM_RENTAL_HELLOWORLD_CANCEL_AUTO_RENEWALS',
            'COM_RENTAL_HELLOWORLD_CANCEL_AUTO_RENEWALS_BUTTON',
            'COM_RENTAL_HELLOWORLD_CANCEL_AUTO_RENEWALS_CLICK_HERE')
    );

    $state = JArrayHelper::getValue($states, (int) $value, $states[0]);

    $html .= '<a rel="tooltip" class="" href="' . JRoute::_('index.php?option=com_rental&task=' . $state[1] . '&id=' . (int) $id) . '" title="' . JText::_($state[4]) . '">';
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
  public static function progressButton($listing_id = '', $unit_id = '', $controller = '', $action = 'edit', $icon = '', $button_text = '', $item = '', $urlParam = 'property_id', $btnClass = '', $current_view = '') {

    $progress_icon = 'warning';
    $okay_icon = 'ok';

    $html = '';

    $id = ($controller == 'propertyversions') ? $listing_id : $unit_id;

    if (!empty($item->latitude) && ($controller == 'propertyversions')) {

      $progress_icon = $okay_icon;
      $id = $listing_id;
    } elseif ($controller == 'contactdetails') {

      if ($item->use_invoice_details) {
        $progress_icon = $okay_icon;
      } elseif (!$item->use_invoice_details && !empty($item->first_name) && !empty($item->surname) && !empty($item->email_1) && !empty($item->phone_1)) {
        $progress_icon = $okay_icon;
      }
      $id = $listing_id;
    } elseif (empty($item->title) && ($controller == 'propertyversions' )) {

      $progress_icon = $progress_icon;
      $id = $listing_id;
    } elseif (empty($item->unit_title) && $controller == 'unitversions' && !empty($listing_id) && $action == 'edit') { // This property has no unit, or unit details not completed...
      $progress_icon = $progress_icon;
    } elseif (!empty($unit_id) && $controller == 'images') {

      $progress_icon = ($item->images > 0) ? $okay_icon : $progress_icon;
    } elseif (!empty($unit_id) && $controller == 'availability') {

      $progress_icon = ($item->availability > 0) ? $okay_icon : $progress_icon;
    } elseif (!empty($unit_id) && $controller == 'unitversions' && !empty($listing_id)) {

      $progress_icon = $okay_icon;
    } elseif (!empty($unit_id) && $controller == 'tariffs') {

      $progress_icon = ($item->tariffs > 0) ? $okay_icon : $progress_icon;
    } else if ($controller == 'reviews') {

      $id = $unit_id;
      $progress_icon = '';
    } elseif (!empty($unit_id) && $controller == 'unitversions') {

      $progress_icon = '';
    }

    $active = ($controller == $current_view) ? 'active' : '';

    if (!$btnClass) {
      $html .= '<li class="' . $active . '">';
    }
    $html .='<a class="' . $btnClass . '"'
            . ' href="' . JRoute::_('index.php?option=com_rental&task=' . $controller . '.' . $action . '&' . $urlParam . '=' . (int) $id . '&' . JSession::getFormToken() . '=1') . '"'
            . ' rel="tooltip">';
    if ($icon) {
      $html .= '<i class="icon icon-' . $icon . '"></i>';
    }
    $html .= '&nbsp;' . Jtext::_($button_text);
    if (!empty($progress_icon) && $icon) {
      $html .= '&nbsp;<i class="icon icon-' . $progress_icon . '"></i>';
    }
    $html .= '</a>';

    if (!$btnClass) {
      '</li>';
    }

    return $html;
  }

  public static function progressMultiTabs($controller = '', $action = 'edit', $icon = '', $button_text = '', $data = '', $urlParam = '', $btnClass = '', $current_view) {

    $html = '';

    $html.='<li class="dropdown">'
            . '<a class="dropdown-toggle"'
            . 'data-toggle="dropdown"'
            . 'href="#">'
            . '';
    $html.= JText::_('Switch unit')
            . '<b class="caret"></b></a>'
            . '<ul class="dropdown-menu">';

    foreach ($data as $item) {

      $html.='<li>';
      $html.=JHtmlProperty::progressButton($item->id, $item->unit_id, $controller, $action, $icon, $item->unit_title, $item, $urlParam, $btnClass, '');

      $html.='</li>';
    }
    $html.='</ul></li>';

    return $html;
  }

}