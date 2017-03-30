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
  public static function getRealEstate()
  {

    // Get the user ID
    $user = JFactory::getUser();

    // Create a new query object.
    $db = JFactory::getDBO();
    $query = $db->getQuery(true);
    $date = JFactory::getDate()->calendar('Y-m-d');

    // Initialise the query.
    $query->select('
        a.id,
        a.expiry_date,
        a.review,
        b.title,
        b.city,
        b.department,
        b.price,
        b.base_currency,
        b.use_invoice_details,
        date_format(a.expiry_date, "%D %M %Y") as expiry_date,
        date_format(a.created_on, "%D %M %Y") as created_on,
        date_format(a.modified, "%D %M %Y") as modified,
        b.latitude,
        b.longitude,
        f.image_file_name as thumbnail,
        (select count(*) from #__vouchers v where a.created_by = ' . (int) $user->id . ' and v.property_id = a.id and v.state = 1' . ' and v.end_date >= ' . $db->quote($date) . ' and v.item_cost_id = ' . $db->quote("1006-002") . ' ) as payment

      ');
    $query->from('#__realestate_property as a');
    $query->join('inner', '#__realestate_property_versions as b on (a.id = b.realestate_property_id and b.id = (select max(c.id) from #__realestate_property_versions as c where c.realestate_property_id = a.id))');
    $query->join('left', '#__user_profile_fc d on a.created_by = d.user_id');
    $query->join('left', '#__users e on a.created_by = e.id');
    $query->join('left', '#__realestate_property_images_library f on b.id = f.version_id');
    $query->where('(f.ordering = (select min(ordering) from #__realestate_property_images_library g where g.version_id = b.id) or f.ordering is null)');

    $query->where('a.created_by=' . (int) $user->id);
    $query->where('a.published !=-2');
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

  /**
   * Get a list of real estate properties owned by the logged in user.
   *
   * @param   JRegistry  &$params  The module parameters.
   *
   * @return  mixed  An array of articles, or false on error.
   */
  public static function getList()
  {
    $user = JFactory::getuser();
    $db = JFactory::getDbo();
    $date = JFactory::getDate()->calendar('Y-m-d');
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
      (select count(*) from #__vouchers v left join #__item_costs b on b.code = v.item_cost_id where a.created_by = ' . (int) $user->id . ' and v.property_id = a.id and v.state = 1' . ' and v.end_date >= ' . $db->quote($date) . ' and b.catid = 65 ) as payment,
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

    // Below corrected from where d.ordering = 1 or is null and published = 1
    $query->where('(d.ordering = (select min(ordering) from #__unit h where h.published = 1))');
    $query->where('a.published !=-2');
    // Join the images, innit!
    $query->join('left', '#__property_images_library f on e.id = f.version_id');
    $query->where('(f.ordering = (select min(ordering) from #__property_images_library g where g.version_id = e.id) or f.ordering is null)');

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
      $property->days_to_renewal = PropertyHelper::getDaysToExpiry($property->expiry_date);
      $property->auto_renewal = (!empty($property->VendorTxCode)) ? true : false;
      // Done properly, can just pass the object here
      $property->message =
              ModListingHelper::getListingStatusMessage($property->expiry_date, $property->days_to_renewal, $property->id, $property->review, $property->payment);
    }
    return $properties;
  }

  public static function getRealestatePropertyList($properties = array())
  {

    foreach ($properties as $property)
    {
      $property->days_to_renewal = PropertyHelper::getDaysToExpiry($property->expiry_date);
      // Done properly, can just pass the object here
      $property->message =
              ModListingHelper::getRealestateStatusMessage($property->expiry_date, $property->days_to_renewal, $property->id, $property->review, $property->payment);
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
  public static function getRealestateStatusMessage($expiry_date, $days_to_renewal, $id, $review, $payment)
  {

    $html = '';

    if ($review == 2)
    {
      $html = JHtml::_('property.locked', 'COM_RENTAL_PROPERTY_LOCKED_FOR_EDITING', 'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON', 'COM_RENTAL_HELLOWORLD_EDIT_LISTING_LOCKED_BUTTON_TOOLTIP');
    }
    elseif (!empty($payment))
    {
      $msg = JText::_('COM_RENTAL_PAYMENT_DUE');
      $html = JHtml::_('property.listingmessage', 'alert alert-info', $msg, 'btn btn-info', 'payment.summary', $id, 'icon icon-chevron-right', 'COM_RENTAL_PAYMENT_DUE_PROCEED', false, 'com_realestate');
    }
    elseif ($review == 1 && !empty($expiry_date) && $days_to_renewal > 28)
    {
      $msg = JText::_('COM_RENTAL_RENTAL_EDIT_NON_SUBMITTED');
      $html = JHtml::_('property.note', 'alert alert-info', $msg, $id, 'com_realestate','propertyversions.edit');
    }
    elseif ($days_to_renewal <= 28 && $days_to_renewal >= 7 && !empty($days_to_renewal))
    {
      $msg = JText::sprintf('COM_RENTAL_CONTROL_PANEL_DAYS_TO_RENEWAL', $days_to_renewal, $expiry_date);
      $html = JHtml::_('property.listingmessage', 'alert alert-info', $msg, 'btn btn-info', 'payment.summary', $id, 'icon icon-chevron-right', 'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON', true, 'com_realestate');
    }
    elseif ($days_to_renewal <= 7 && $days_to_renewal >= 0 && !empty($days_to_renewal))
    {
      $msg = JText::sprintf('COM_RENTAL_CONTROL_PANEL_DAYS_TO_RENEWAL', $days_to_renewal, $expiry_date);
      $html = JHtml::_('property.listingmessage', 'alert alert-warning', $msg, 'btn btn-warning', 'payment.summary', $id, 'icon icon-chevron-right', 'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON', true, 'com_realestate');
    }
    elseif ($days_to_renewal < 0 && !empty($days_to_renewal))
    {
      $msg = JText::sprintf('COM_RENTAL_OWNERS_CONTROL_PANEL_PROPERTY_EXPIRED', $expiry_date);
      $html = JHtml::_('property.listingmessage', 'alert alert-danger', $msg, 'btn btn-danger', 'payment.summary', $id, 'icon icon-chevron-right', 'COM_RENTAL_HELLOWORLD_RENEW_NOW_BUTTON', true, 'com_realestate');
    }
    elseif (empty($days_to_renewal))
    {
      $msg = JText::_('COM_RENTAL_OWNERS_CONTROL_PANEL_PROPERTY_NOT_COMPLETED');
      $html = JHtml::_('property.note', 'alert alert-info', $msg, $id, 'com_realestate', 'propertyversions.edit');
    }
    else
    {
      $msg = JText::_('COM_RENTAL_OWNERS_CONTROL_PANEL_EDIT_PROPERTY');
      $html = JHtml::_('property.note', 'alert alert-info', $msg, $id, 'com_realestate', 'propertyversions.edit');
    }

    return $html;
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
  public static function getListingStatusMessage($expiry_date, $days_to_renewal, $id, $review, $payment)
  {

    $html = '';

    if ($review == 2)
    {
      $html = JHtml::_('property.locked', 'COM_RENTAL_PROPERTY_LOCKED_FOR_EDITING', 'COM_RENTAL_HELLOWORLD_EDIT_LISTING_BUTTON', 'COM_RENTAL_HELLOWORLD_EDIT_LISTING_LOCKED_BUTTON_TOOLTIP');
    }
    elseif (!empty($payment))
    {
      $msg = JText::_('COM_RENTAL_PAYMENT_DUE');
      $html = JHtml::_('property.listingmessage', 'alert alert-info', $msg, 'btn btn-info', 'payment.summary', $id, 'icon icon-chevron-right', 'COM_RENTAL_PAYMENT_DUE_PROCEED', false);
    }
    elseif ($review == 1 && !empty($expiry_date) && $days_to_renewal > 28)
    {
      $msg = JText::_('COM_RENTAL_RENTAL_EDIT_NON_SUBMITTED');
      $html = JHtml::_('property.note', 'alert alert-info', $msg, $id);
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
      $html = JHtml::_('property.note', 'alert alert-info', $msg, $id);
    }
    else
    {
      $msg = JText::_('COM_RENTAL_OWNERS_CONTROL_PANEL_EDIT_PROPERTY');
      $html = JHtml::_('property.note', 'alert alert-info', $msg, $id);
    }

    return $html;
  }

}
