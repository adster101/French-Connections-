<?php

/**
 * @package    Joomla.Cli
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// Initialize Joomla framework
        const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php'))
{
  require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES'))
{
  define('JPATH_BASE', dirname(__DIR__));
  require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class RealestateImages extends JApplicationCli
{

  /**
   * Entry point for the script
   *
   * @return  void
   *
   * @since   2.5
   */
  public function doExecute()
  {
    define('COM_IMAGE_BASE', JPATH_ROOT . '/images/property/');

    // Add and get an instance of the realestate model image thingy
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_realestate/models');
    $model = JModelLegacy::getInstance('Image', 'RealestateModel');

    // Script to process real estate images for all properties in the #__realestate_property table
    $images = $this->_getProps();

    foreach ($images as $image)
    {
      $baseDir[] = COM_IMAGE_BASE . $image->realestate_property_id . '/gallery/';
      $baseDir[] = COM_IMAGE_BASE . $image->realestate_property_id . '/thumbs/';
      $baseDir[] = COM_IMAGE_BASE . $image->realestate_property_id . '/thumb/';

      // The base path for the images created below
      $filepath = COM_IMAGE_BASE . $image->realestate_property_id;

      // 
      $image_path = $filepath . '/' . $image->image_file_name;

      // Image has been uploaded, let's create some image profiles...
      try
      {
        $model->generateImageProfile($image_path, (int) $image->realestate_property_id, $image->image_file_name, 'gallery', 578, 435);
        $model->generateImageProfile($image_path, (int) $image->realestate_property_id, $image->image_file_name, 'thumbs', 100, 100);
        $model->generateImageProfile($image_path, (int) $image->realestate_property_id, $image->image_file_name, 'thumb', 210, 120);
      }
      catch (Exception $e)
      {
        JLog::add($e->getMessage() . ' - ' . $image->image_file_name . '(' . $image->realestate_property_id . ')', JLog::ERROR, 'import_images');
      }
    }
  }

  /*
   * Get a list of properties due to expire and are set to manual renewal
   */

  private function _getProps()
  {

    $this->out('Getting props and images...');

    $db = JFactory::getDBO();
    /**
     * Get the date now
     */
    $date = JFactory::getDate();

    $query = $db->getQuery(true);
    $query->select('a.id as realestate_property_id, b.image_file_name'
    );

    $query->from('#__realestate_property a');

    $query->join('left', '#__realestate_property_images_library b on a.id = b.realeste_property_id');
    $query->where('b.id is not null');

    $db->setQuery($query);

    try
    {
      $rows = $db->loadObjectList();
    }
    catch (Exception $e)
    {
      $this->out('Problem getting props...');
      return false;
    }

    return $rows;
  }

}

JApplicationCli::getInstance('RealestateImages')->execute();
