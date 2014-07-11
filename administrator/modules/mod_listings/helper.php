<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  mod_latest
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Helper for mod_latest
 *
 * @package     Joomla.Administrator
 * @subpackage  mod_latest
 * @since       1.5
 */
abstract class ModListingHelper
{

  /**
   * Get a list of properties owned by the logged in user.
   *
   * @param   JRegistry  &$params  The module parameters.
   *
   * @return  mixed  An array of articles, or false on error.
   */
  public static function getList()
  {
    $user = JFactory::getuser();
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    // Select some fields
    $query->select('
      a.id,
      b.title,
      a.created_by,
      a.published,
      e.unit_id,
      date_format(a.expiry_date, "%D %M %Y") as expiry_date,
      date_format(a.created_on, "%D %M %Y") as created_on,
      date_format(a.modified, "%D %M %Y") as modified,
      a.VendorTxCode,
      a.review,
      f.image_file_name as thumbnail
    ');

    $query->where('a.created_by=' . (int) $user->id);

    $query->from('#__property as a');
    $query->join('inner', '#__property_versions as b on (
      a.id = b.property_id
      and b.id = (select max(c.id) from #__property_versions c where c.property_id = a.id)
    )');

    // Join the units for the image
    $query->join('left', '#__unit d on d.property_id = a.id');
    $query->join('left', '#__unit_versions e on (d.id = e.unit_id and e.id = (select max(f.id) from #__unit_versions f where unit_id = d.id))');
    $query->where('(d.ordering = 1 or d.ordering is null)');

    // Join the images, innit!
    $query->join('left', '#__property_images_library f on e.id = f.version_id');
    $query->where('(f.ordering = 1 or f.ordering is null)');

    $db->setQuery($query);

    try
    {
      $items = $db->loadObjectList();
    }
    catch (Exception $e)
    {
      return false;
    }

    return $items;
  }

  public static function getPropertyList(array $properties)
  {

    foreach ($properties as $property)
    {
      $property->days_to_renewal = RentalHelper::getDaysToExpiry($property->expiry_date);
      $property->auto_renewal = (!empty($property->VendorTxCode)) ? true : false;
      // Done properly, can just pass the object here 
      $property->message =
              ModListingHelper::getListingStatusMessage($property->expiry_date, $property->days_to_renewal, $property->id, $property->review);
    }
    return $properties;
  }

  /**
   * Helper function to generate a message and button for a listing depending on it's $expiry_date
   * 
   * @param type $expiry_date
   * @param type $days_to_renewal
   * @param type $id
   * @param type $review
   * @return type
   */
  public static function getListingStatusMessage($expiry_date, $days_to_renewal, $id, $review)
  {

    $html = '';

    if ($review == 2)
    {
      $msg = JText::_('COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON');
      $html = JHtml::_('property.locked', $msg);
    }
    elseif ($review == 1 && !empty($expiry_date) && $days_to_renewal > 28)
    {
      $msg = JText::_('COM_RENTAL_RENTAL_EDIT_NON_SUBMITTED');
      $html = JHtml::_('property.note', 'alert alert-info', $msg);
    }
    elseif ($days_to_renewal <= 28 && $days_to_renewal >= 7 && !empty($days_to_renewal))
    {
      $msg = JText::sprintf('COM_RENTAL_CONTROL_PANEL_DAYS_TO_RENEWAL', $days_to_renewal, $expiry_date);
      $html = JHtml::_('property.listingmessage', 'alert alert-info', $msg, 'btn btn-info', 'payment.summary', $id, 'icon icon-chevron-right', 'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON', true);
    }
    elseif ($days_to_renewal <= 7 && $days_to_renewal >= 0 && !empty($days_to_renewal))
    {
      $msg = JText::sprintf('COM_RENTAL_CONTROL_PANEL_DAYS_TO_RENEWAL', $days_to_renewal, $expiry_date);
      $html = JHtml::_('property.listingmessage', 'alert alert-warning', $msg, 'btn btn-warning', 'payment.summary', $id, 'icon icon-chevron-right', 'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON', true);
    }
    elseif ($days_to_renewal < 0 && !empty($days_to_renewal))
    {
      $msg = JText::sprintf('COM_RENTAL_OWNERS_CONTROL_PANEL_PROPERTY_EXPIRED', $expiry_date);
      $html = JHtml::_('property.listingmessage', 'alert alert-danger', $msg, 'btn btn-danger', 'payment.summary', $id, 'icon icon-chevron-right', 'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON', true);
    }
    elseif (empty($days_to_renewal))
    {
      $msg = JText::_('COM_RENTAL_OWNERS_CONTROL_PANEL_PROPERTY_NOT_COMPLETED');
      $html = JHtml::_('property.note', 'alert alert-danger', $msg);
    }

    return $html;
  }

}
