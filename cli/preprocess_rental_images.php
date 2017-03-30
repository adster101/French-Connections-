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
class PreProcessRentalImages extends JApplicationCli
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
   
    // Create a log file for the email kickers
    jimport('joomla.error.log');

    jimport('joomla.filesystem.folder');

    JLog::addLogger(array('text_file' => 'images.import.php'), JLog::ALL, array('import_images'));

 
    // The source folder for the pics 
    //$src = '/home/adam/Pictures/_images';
    $src = 'D:\Pics\_images';
     
    // Get a list of all images in the property image library table...
    $images = $this->_getImages();

    foreach ($images as $image)
    {
      
      // The source path for the image being processed
      $image_path = $src . '/' . $image->image_file_name;

      try
      {    
        
        $image_path_to_copy = 'C:\xampp\htdocs\images\property' . '/' . (int) $image->unit_id . '/' . $image->image_file_name;

        // If file exists in original image path move it to the unit folder
        if (file_exists($image_path) && !file_exists($image_path_to_copy))
        {
          $move = copy($image_path, $image_path_to_copy);

          if (!$move)
          {
            throw new Exception('Problem moving image ' . $image->image_file_name . ' for unit ' . $image->unit_id);
          }

          $baseDir[] = 'C:\xampp\htdocs\images\property' . '/' . (int) $image->unit_id . '/gallery/';
          $baseDir[] = 'C:\xampp\htdocs\images\property' . '/' . (int) $image->unit_id . '/thumbs/';
          $baseDir[] = 'C:\xampp\htdocs\images\property' . '/' . (int) $image->unit_id . '/thumb/';

          // Create folders for each of the profiles for the property, if they don't exist
          foreach ($baseDir as $dir)
          {
            if (file_exists($dir))
            {
              JFolder::delete($dir);
            }
          }
        }
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

  private function _getImages()
  {

    $this->out('Getting props and images...');

    $db = JFactory::getDBO();

    $query = $db->getQuery(true);
    $query->select('a.id as unit_id, b.image_file_name');

    $query->from('#__unit a');

    $query->join('left', '#__property_images_library b on a.id = b.unit_id');
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

JApplicationCli::getInstance('PreProcessRentalImages')->execute();
