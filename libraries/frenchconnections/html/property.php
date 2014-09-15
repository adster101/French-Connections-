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
class JHtmlProperty
{

  /**
   * Display an image.
   *
   * @param   string  $src  The source of the image
   *
   * @return  string  A <img> element if the specified file exists, otherwise, a null string
   *
   * @since   2.5
   */
  public static function image($src)
  {
    $src = preg_replace('#[^A-Z0-9\-_\./]#i', '', $src);
    $file = JPATH_SITE . '/' . $src;

    jimport('joomla.filesystem.path');
    JPath::check($file);

    if (!file_exists($file))
    {
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
  public static function addNote($property_id)
  {
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
  public static function filterNotes($count, $userId)
  {
    if (empty($count))
    {
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
  public static function notes($id)
  {
    if (empty($id))
    {
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
  public static function stats($id, $u_id)
  {

    if (empty($id))
    {
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
  public static function reviewStates()
  {



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
  public static function editButton($days = '', $id = '', $unit_id = '', $review = '')
  {
    // Array of image, task, title, action.
    // Possible renewal states are
    // Expired (renew now)
    // About to expire (non auto-renew) (renew now) (opt in)
    // About to expire (auto renew) (opt out)
    // Publshed with > 28 days to renewal (non auto renew) (opt in)

    $value = '';
    $html = '';
    $allowEdit = true;

    if (empty($days) || $days > 28)
    { // A new sign up which has never been published...
      $value = 2;
    }
    elseif ($days <= 7 && $days >= 0)
    { // Property about to expire
      $value = 1;
    }
    elseif ($days < 0 && !empty($days))
    { // Property has expired
      $value = 1;
    }
    elseif ($days >= 7 && $days <= 28)
    { // More than seven days but expiring within the month.
      $value = 3;
    }

    if ($review == 2)
    {
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
    if ($allowEdit)
    {
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
    }
    else
    {

      $html .= '<span rel="tooltip" class="btn ' . $state[3] . '" title="' . JText::_($state[2]) . '">';
      $html .= '<i class="icon icon-locked">&nbsp;</i>';
    }
    $html .= JText::_($state[2]);

    $html.= ($allowEdit) ? '</a>' : '</span>';
    $html .= '</div>';
    return $html;
  }

  /**
   * This helper method simply displays a button on the property listings view based on the review 
   * state and the number of days until renewal.
   * 
   * @param	int $days	The number of days until the property expires, or null if a new sign up
   * @param	int $i
   */
  public static function renewalButton($days = '', $id = '', $review = 0)
  {

    // Array of image, task, title, action.
    // Possible renewal states are
    // Expired (renew now)
    // About to expire (non auto-renew) (renew now) (opt in)
    // About to expire (auto renew) (opt out)
    // Publshed with > 28 days to renewal (non auto renew) (opt in)

    $html = '';

    if (empty($days) || $days > 28)
    {
      // A new sign up which has never been published...
      $html = JHtml::_('property.link', $id, 'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON_TOOLTIP', 'listing.view', 'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON', 'btn btn-primary', false);
    }
    elseif ($days <= 7 && $days >= 0)
    {
      // Property about to expire
      $html = JHtml::_('property.link', $id, 'COM_RENTAL_HELLOWORLD_RENEW_NOW_ABOUT_TO_EXPIRE_TOOLTIP', 'payment.summary', 'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON', 'btn btn-warning', true);
    }
    elseif ($days < 0 && !empty($days))
    {
      // Property has expired
      $html = JHtml::_('property.link', $id, 'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON_TOOLTIP', 'payment.summary', 'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON', 'btn btn-danger', true);
    }
    elseif ($days >= 7 && $days <= 28)
    {
      // More than seven days but expiring within the month.
      $html = JHtml::_('property.link', $id, 'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON_TOOLTIP', 'payment.summary', 'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON', 'btn btn-danger', true);
    }
    elseif ($review == 2)
    {
      $msg = JText::_('COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON');
      $html = JHtml::_('property.locked', $msg);
    }

    return $html;
  }

  /**
   * @param	int $value	The state value
   * @param	int $i
   */
  public static function autorenewalstate($value = '', $id = '')
  {

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

  public static function button($btnClass = '', $task = '', $iconClass = '', $text)
  {

    $html = '';
    $html.='<button class="' . $btnClass . '" onclick="Joomla.submitbutton(\'' . $task . '\')">'
            . '<i class="' . $iconClass . '"></i>&nbsp;&nbsp;'
            . JText::_($text)
            . '</button>';

    return $html;
  }

  /*
   * A generic make button function button
   *
   *
   */

  public static function quicklink($title = '', $url = '', $text = '')
  {

    $html = '';
    $html .= '<p>'
            . '<a title="' . JText::_($title) . '" href="' . $url . '">'
            . JText::_($text)
            . '</a>'
            . '</p>';
    return $html;
  }

  /*
   * A generic make button function button
   *
   *
   */

  public static function note($msgClass = 'alert alert-danger', $msg = '', $id)
  {
    $link = JHtml::_('property.link', $id, 'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON_TOOLTIP', 'listing.view', 'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON', 'btn btn-primary', false);
    $html = '';
    $html .= '<div class="' . $msgClass . '"><span class="icon icon-info">&nbsp;</span>&nbsp;' . JText::_($msg);
    $html .= '&nbsp;' . $link
            .= '</div>';
    return $html;
  }

  /*
   * A generic make button function button
   *
   *
   */

  public static function locked($msg, $btnText, $title)
  {
    $html = '<div class="alert alert-info">'
            . JText::_($msg)
            . '&nbsp;<button rel="tooltip" class="btn btn-primary disabled" title="' . JText::_($title) . '">'
            . '<i class="icon icon-locked"> </i>&nbsp;'
            . JText::_($btnText)
            . '</button></div>';
    return $html;
  }

  /**
   * Adds a link type affair to the page
   * 
   * @param type $id
   * @param type $title
   * @param type $task
   * @param type $text
   * @param type $renewal
   * @return string
   */
  public static function link($id = '', $title = '', $task = '', $text = '', $class = '', $renewal = false)
  {
    $isRenewal = ($renewal) ? '&renewal=1' : '';
    $route = JRoute::_('index.php?option=com_rental&task=' . $task . '&id=' . (int) $id . $isRenewal);
    $html = '';
    $html .= '<a rel="tooltip" title="' . JText::_($title) . '" href="' . $route . '" class="' . $class . '">'
            . '<i class="icon icon-chevron-right"></i>&nbsp;'
            . JText::_($text)
            . '</a>';
    return $html;
  }

  /*
   * A generic make button function button
   *
   *
   */

  public static function listingmessage($msgClass = 'alert alert-danger', $msg = '', $btnClass = 'btn btn-danger', $task = '', $id, $iconClass = '', $btnText = '', $renewal = false)
  {
    $isRenewal = ($renewal) ? '&renewal=1' : '';
    $route = JRoute::_('index.php?option=com_rental&task=' . $task . '&id=' . (int) $id . $isRenewal);
    $link = 'index.php?option=com_rental&task=listing.view&id=' . (int) $id;
    $html = '';
    $html .= '<div class="' . $msgClass . ' clearfix">' . JText::_($msg)
            . '&nbsp;&nbsp;<a class="' . $btnClass . '" href="' . $route . '">'
            . '<i class="' . $iconClass . '">&nbsp;</i>&nbsp;'
            . JText::_($btnText)
            . '</a>&nbsp;|&nbsp;'
            . '<a href="' . $link . '">'
            . 'Edit this property'
            . '</a>'
            . '</div>';
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
  public static function getActions($extension, $categoryId = 0)
  {
    $user = JFactory::getUser();
    $result = new JObject;
    $parts = explode('.', $extension);
    $component = $parts[0];

    if (empty($categoryId))
    {
      $assetName = $component;
      $level = 'component';
    }
    else
    {
      $assetName = $component . '.category.' . (int) $categoryId;
      $level = 'category';
    }

    $actions = JAccess::getActions($component, $level);

    foreach ($actions as $action)
    {
      $result->set($action->name, $user->authorise($action->name, $assetName));
    }

    return $result;
  }

  /**
   * @param	int $value
   * @param	int $i
   */
  public static function progressButton($action = '', $state = false)
  {

    $layout = new JLayoutHelper();

    $options = array(
        'doTask' => "Joomla.submitbutton('$action')",
        'class' => 'publish',
        'text' => 'Blah',
        'btnClass' => 'btn btn-default'
    );

    $html = $layout->render('joomla.toolbar.standard', $options);





    return $html;
  }

}
