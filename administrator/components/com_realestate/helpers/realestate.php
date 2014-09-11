<?php

// No direct access to this file
defined('_JEXEC') or die;

/**
 * HelloWorld component helper.
 */
abstract class RealestateHelper
{

  /**
   * Method to return the number of days until the property is due to expire
   *
   * @param type $expiry_date
   */
  public static function getDaysToExpiry($expiry_date = '')
  {

    $expiry_date = (!empty($expiry_date)) ? new DateTime($expiry_date) : '';

    if ($expiry_date)
    {
      $now = new DateTime(date('Y-m-d'));
      $days_to_renewal = $now->diff($expiry_date)->format('%R%a');
    }

    $days_to_renewal = (!empty($days_to_renewal)) ? $days_to_renewal : '';

    return $days_to_renewal;
  }

  /*
   * Determines a list of notices to display for a property notifying the user of which units and which sections need attention
   */

  public static function getProgressNotices($progress = array())
  {

    $notices = array();
    // The sections we want to check for. Tariffs needs expanding for the more detailed tariff data (changeover day etc)
    $sections = array('images' => array(), 'availability' => array(), 'tariffs' => array());

    if (empty($progress))
    {
      return false;
    }

    foreach ($progress as $unit)
    {
      if (empty($unit->unit_title))
      {
        $notices['Accommodation']['units'][] = (!empty($unit->unit_title)) ? $unit->unit_title : 'New Unit';
      }
    }

    if (!$progress[0]->use_invoice_details &&
            empty($progress[0]->first_name) &&
            empty($progress[0]->surname) &&
            empty($progress[0]->email_1) &&
            empty($progress[0]->phone_1)
    )
    {
      $notices['contact_details']['units'] = false;
    }

    foreach ($sections as $section => $value)
    {

      foreach ($progress as $key => $unit)
      {
        if ($unit->$section == 0)
        { // If the unit doesn't have this section completed
          if (!array_key_exists($section, $notices))
          {
            $notices[$section]['units'] = '';
          }

          // Add the unit that is failing to the list
          $notices[$section]['units'][] = (!empty($unit->unit_title)) ? $unit->unit_title : 'New Unit';
        }
      }
    }
    return $notices;
  }

  /*
   * Get a list of filter options for the review state of a property
   *
   * @return  array An array of JHtmlOption elements
   */

  public static function getReviewOptions()
  {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '1', JText::_('COM_RENTAL_HELLOWORLD_UPDATED'));
    $options[] = JHtml::_('select.option', '2', JText::_('COM_RENTAL_HELLOWORLD_FOR_REVIEW'));
    return $options;
  }

  /*
   * Get a list of filter options for the snooze state of a property
   *
   * @return  array An array of JHtmlOption elements
   */

  public static function getSnoozeOptions()
  {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '1', JText::_('COM_RENTAL_HELLOWORLD_HIDE_SNOOZED'));
    $options[] = JHtml::_('select.option', '2', JText::_('COM_RENTAL_HELLOWORLD_SHOW_SNOOZED'));
    return $options;
  }

  /*
   * Get a list of filter options for the snooze state of a property
   *
   * @return  array An array of JHtmlOption elements
   */

  public static function getDateFilterOptions()
  {
    // Build the filter options.
    $options = array();
    $options[] = JHtml::_('select.option', '', JText::_('JSELECT'));
    $options[] = JHtml::_('select.option', 'expiry_date', 'Expiry date');
    $options[] = JHtml::_('select.option', 'created_on', 'Date created');
    return $options;
  }

  
  /**
   * Get the actions
   */
  public static function getActions($assetName = 'com_rental')
  {
    $user = JFactory::getUser();
    $result = new JObject;

    $actions = array(
        'core.admin',
        'core.manage',
        'core.create',
        'core.delete',
        'core.edit',
        'core.edit.state',
        'core.edit.own',
        'rental.unit.reorder',
        'rental.listing.submit',
        'rental.listing.review',
        'rental.listing.admin',
        'rental.listings.showowner',
        'rental.listings.filter',
        'rental.notes.view',
        'rental.notes.add',
        'rental.images.delete',
        'rental.images.reorder',
    );


    foreach ($actions as $action)
    {
      $result->set($action, $user->authorise($action, $assetName));
    }
    return $result;
  }

  /* Method to determine whether the currently logged in user is an 'owner' or an admin or something else...
   *
   *
   */

  public static function isOwner($editUserID = null)
  {

    // Get the user object and assign the userID to a var
    $user = JFactory::getUser($editUserID);


    // Get a list of the groups that the user is assigned to
    $groups = JAccess::getGroupsByUser($user->id, true);

    if (in_array(10, $groups))
    {
      return true;
    }
    else
    {

      return false;
    }
  }

  

  

}
