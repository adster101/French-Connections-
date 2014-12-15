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
class RentalImages extends JApplicationCli
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

    // The source folder for the pics 
    //$src = '/home/adam/Pictures/_images';
    $src = 'D:\Pics\_images';

    // Add and get an instance of the realestate model image thingy
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');
    $model = JModelLegacy::getInstance('Image', 'RentalModel');

    // Script to process real estate images for all properties in the #__realestate_property table
    $images = $this->_getProps();

    foreach ($images as $image)
    {
      $baseDir[] = COM_IMAGE_BASE . $image->unit_id . '/gallery/';
      $baseDir[] = COM_IMAGE_BASE . $image->unit_id . '/thumbs/';
      $baseDir[] = COM_IMAGE_BASE . $image->unit_id . '/thumb/';

      // The source path for the image being processed
      $image_path = $src . '/' . $image->image_file_name;

      // Image has been uploaded, let's create some image profiles...
      try
      {
        $model->generateImageProfile($image_path, (int) $image->unit_id, $image->image_file_name, 'gallery', 578, 435);
        $model->generateImageProfile($image_path, (int) $image->unit_id, $image->image_file_name, 'thumbs', 100, 100);
        $model->generateImageProfile($image_path, (int) $image->unit_id, $image->image_file_name, 'thumb', 210, 120);
      }
      catch (Exception $e)
      {
        JLog::add($e->getMessage() . ' - ' . $image->image_file_name . '(' . $image->unit_id . ')', JLog::ERROR, 'import_images');
      }
      
      $this->out('Done image...' . $image->image_file_name);

    }
  }

  /*
   * Get a list of properties due to expire and are set to manual renewal
   */

  private function _getProps()
  {

    $this->out('Getting props and images...');

    $db = JFactory::getDBO();


    $query = $db->getQuery(true);
    $query->select('a.id as unit_id, b.image_file_name');

    $query->from('#__unit a');

    $query->join('left', '#__property_images_library b on a.id = b.unit_id');
    $query->where('b.id is not null');
    $query->where('a.property_id = 5374');

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

JApplicationCli::getInstance('RentalImages')->execute();
