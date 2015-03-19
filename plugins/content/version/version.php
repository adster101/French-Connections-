<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Content.joomla
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Example Content Plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Content.joomla
 * @since       1.6
 */
class plgContentVersion extends JPlugin
{

  /**
   * Example after save content method
   * Article is passed by reference, but after the save, so no changes will be saved.
   * Method is called right after the content is saved
   *
   * @param   string  The context of the content passed to the plugin (added in 1.6)
   * @param   object		A JTableContent object
   * @param   bool		If the content is just about to be created
   * @since   1.6
   */
  public function onContentBeforeBind($context, $article, $isNew, $data)
  {

    $new_version = false;

    // Here we check whether we are already editing an unpublished new version of this item
    // So if we are then we can skip all the comparing and simply save straight over the new/unpublished version

    if ($data['review'])
    {
      return true;
    }
    else
    {
      // A list of fields to check through which will trigger the creation of a new version
      $fields_to_check = array(
          'title' => 1,
          'unit_title' => 1,
          'description' => 1,
          'internal_facilities_other' => 1,
          'external_facilities_other' => 1,
          'activities_other' => 1,
          'location_details' => 1,
          'getting_there' => 1,
          'additional_price_notes' => 1,
          'linen_costs' => 1,
          'first_name' => 1,
          'surname' => 1,
          'phone_1' => 1,
          'phone_2' => 1,
          'phone_3' => 1,
          'fax' => 1,
          'email_1' => 1,
          'email_2' => 1,
      );

      // Check we are handling a property manager form.
      if ($context != 'com_rental.unitversions' && $context != 'com_rental.propertyversions' && $context != 'com_rental.contactdetails')
      {
        return false;
      }
      // Check if this is a new article.
      if ($isNew)
      {
        // New article so no need for a new version
        return false;
      }






      //Check if there is an expiry date for this content, if not then just return out...
      if (!$data['review'])
      {


      }

      return $new_version;
    }
  }

}
