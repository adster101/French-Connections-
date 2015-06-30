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

jimport('joomla.filesystem.folder');


//require '../vendor/autoload.php';
//use OpenCloud\Rackspace;

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class RentalImages extends JApplicationCli
{

  /**
   * Array to hole the 
   * 
   * @var type array
   */
  public $profiles = array('903x586', '770x580', '617x464', '408x307', '330x248', '210x120', '100x100');

  /**
   * Entry point for the script
   *
   * @return  void
   *
   * @since   2.5
   */
  public function doExecute()
  {
//    try {
//      // Instantiate a Rackspace client.
//      $client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
//          'username' => 'fcadmin01',
//          'apiKey' => '971715d42f3a40d3bcb42f7286477f45'
//      ));
//
//      $objectStoreService = $client->objectStoreService(null, 'LON');
//
//      $container = $objectStoreService->createContainer('images');
//
//      $container = $objectStoreService->getContainer('images');
//    }
//    catch (Exception $e) {
//      var_dump($e);
//      die;
//    }
    // Add and get an instance of the realestate model image thingy
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');
    $model = JModelLegacy::getInstance('Image', 'RentalModel');

    // Script to process real estate images for all properties in the #__realestate_property table
    $images = $this->_getImages();


    foreach ($images as $image)
    {

      // The source path for the image being processed

      $image_path = JPATH_BASE . '/images/property';

      // Image has been uploaded, let's create some image profiles...
      try {
        $this->processImage($image_path, (int) $image->unit_id, $image->image_file_name);
      }
      catch (Exception $e) {
        JLog::add($e->getMessage() . ' - ' . $image->image_file_name . '(' . $image->unit_id . ')', JLog::ERROR, 'import_images');
      }

      $this->out('Done image...' . $image->image_file_name);
    }
  }

  /*
   * 
   * Method to generate a set of profile images for images being uploaded via the image manager
   *
   * Profiles
   * 
   * 903x586
   * 770x580
   * 617x464
   * 408x307
   * 330x248
   * 210x120
   * 100x100
   * 
   * TO DO - Make it send up the images to CDN...and then remove the profile gallery
   * 
   */

  public function processImage($image_path = '', $unit_id = '', $image_file_name = '', $max_width = 903, $max_height = 586)
  {

    $image = $image_path . '/' . $unit_id . '/' . $image_file_name;
    $image_file_path = $image_path . '/' . $unit_id . '/profiles/';

    if (!file_exists($image))
    {
      // Change this to throw exception
      return false;
    }

    // Create a new image object ready for processing
    $imgObj = new JImage($image);

    // Create a folder for the profile, if it doesn't exist
    if (!file_exists($image_file_path))
    {
      JFolder::create($image_file_path);
    }

    try {

      // Image width
      $width = $imgObj->getWidth();

      // Image height
      $height = $imgObj->getHeight();

      // If the width is greater than the height just create it
      if (($width > $height))
      {

        // This image is roughly landscape orientated with a width greater than max possible image width
        $profile = $imgObj->resize($max_width, $max_height, true, 3);

        $thumbs = $profile->generateThumbs($this->profiles, 5);

        // Load the existing image
        // $existing_image = imagecreatefromjpeg($file_path);
        // Make it progressive
        // $bit = imageinterlace($existing_image, 1);
        // Save it out
        // imagejpeg($existing_image, $file_path, 100);
        // Free up memory
        // imagedestroy($existing_image);
      }
      else if ($width < $height)
      {
        // This image is roughly portrait orientation
        $profile = $imgObj->resize($max_width, $max_height, false, 2);
        $thumbs = $profile->generateThumbs($this->profiles, 5);
      }

      // Create a profile for each 
      foreach ($thumbs as $key => $thumb)
      {
        // Put it out to a file
        $file_name = $image_file_path . $this->profiles[$key] . '_' . $image_file_name;
        $thumb->tofile($file_name);

        imagedestroy($thumb);
      }
    }
    catch (Exception $e) {
      $this->out($e->message);
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
    $query->join('left', '#__property c on c.id = a.property_id');
    $query->where('b.id is not null');
    $query->where('c.expiry_date > ' . $db->quote(JHtml::_('date', 'now', 'Y-m-d')));
    $db->setQuery($query);

    try {
      $rows = $db->loadObjectList();
    }
    catch (Exception $e) {
      $this->out('Problem getting props...');
      return false;
    }

    return $rows;
  }

}

JApplicationCli::getInstance('RentalImages')->execute();
