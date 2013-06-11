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
class plgContentVersion extends JPlugin {

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
  public function onContentBeforeBind($context, $article, $isNew, $data) {

    $new_version = false;

    // Here we check whether we are already editing an unpublished new version of this item
    // So if we are then we can skip all the comparing and simply save straight over the new/unpublished version

    if ($data['review']) {
      return true;
    } else {
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
          'linen_costs' => 1
      );

      // Check we are handling a property manager form.
      if ($context != 'com_helloworld.unitversions' && $context != 'com_helloworld.propertyversions') {
        return false;
      }
      // Check if this is a new article.
      if ($isNew) {
        // New article so no need for a new version
        return false;
      }



      $expiry_date = ($article->published_on) ? $article->published_on : '';
      // Parse the date so we can check it's valid
      $date = date_parse($expiry_date);
      $check = checkdate($date['month'], $date['day'], $date['year']);


      //Check if there is an expiry date for this content, if not then just return out...
      if ($check) {

        // Loop over the fields that will trigger a new version and check to see if any differ
        foreach ($article as $field => $value) {
          // Compare the content from the database with the content from the editor
          if (array_key_exists($field, $fields_to_check)) {
            // Compare the two strings...
            $compare = strcmp($article->$field, $data[$field]);
            if ($compare <> 0) {
              $new_version = true;
            }
          }
        }
      }

      return $new_version;
    }
  }

}
