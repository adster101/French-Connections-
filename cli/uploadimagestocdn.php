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

// Include the rackspace bits and bobs
require 'vendor/autoload.php';

use OpenCloud\Rackspace;

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class Uploadimagestocdn extends JApplicationCli
{

  /**
   *  A list of users to ignore - rental and realestate.
   * 
   * @var type 
   * 
   */
  private $users_to_ignore = '9436, 8290, 7931, 9773';

  /**
   * Entry point for the script
   *
   * @return  void
   *
   * @since   2.5
   */
  public function doExecute()
  {
    $images = $this->_getImages();
    
    // Instantiate a Rackspace client.
    $client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
        'username' => 'fcadmin01',
        'apiKey' => '971715d42f3a40d3bcb42f7286477f45'
    ));

    // Obtain an Object Store service object from the client.
    $objectStoreService = $client->objectStoreService(null, 'LON');

    $container = $objectStoreService->getContainer('test');


    foreach ($images as $image)
    {
      if (!$image->cdn)
      {
        // The file path
        $file = JPATH_BASE . '/images/property/' . $image->id . '/' . $image->image_file_name;

        // The cloud 'object' file name
        $file_name = $image->id . '/' . $image->image_file_name;

        // TO DO - here will need to prepend the list of profile sizes to filename in a foreach
        // Open the image for reading
        $handle = fopen($file, 'r');

        // Upload the object to the cloud files server
        $object = $container->uploadObject($file_name, $handle);

        // TO DO - Add error handling here.
        // TO DO - Update image detail in table to indicate image has been upload to cloud file server
        // TO DO - Remove profile pic from file system (possibly to archive?)
      }
    }
  }

  /*
   * Get a list of properties due to expire and are set to manual renewal
   * TO DO - This will be worthwhile doing as a UNION so we can process rental
   * and realestate images in one go
   */

  private function _getImages()
  {

    $this->out('Getting props and images...');

    $db = JFactory::getDBO();

    $query = $db->getQuery(true);

    $query->select('b.unit_id AS id, b.image_file_name, b.cdn');
    $query->from('#__unit a');
    $query->join('left', '#__property_images_library b on a.id = b.unit_id');
    $query->join('left', '#__property c on c.id = a.property_id');
    $query->where('b.id is not null');
    $query->where('c.expiry_date > ' . $db->quote(JHtml::_('date', 'now', 'Y-m-d')));
    $query->where('c.created_by not in (' . $db->quote($this->users_to_ignore) . ')');
    $query->where('b.cdn = 0');

    $union = $db->getQuery(true);

    $union->select('b.realestate_property_id AS id, b.image_file_name, b.cdn');
    $union->from($db->quoteName('#__realestate_property','a'));
    $union->join('left', $db->quoteName('#__realestate_property_images_library', 'b') . 'on a.id = b.realestate_property_id');
    $union->where('b.id is not null');
    $union->where('a.expiry_date > ' . $db->quote(JHtml::_('date', 'now', 'Y-m-d')));
    $union->where('a.created_by not in (' . $db->quote($this->users_to_ignore) . ')');
    $union->where('b.cdn = 0');

    $query->union($union);

    $db->setQuery($query);

    try
    {
      $rows = $db->loadObjectList();
    }
    catch (Exception $e)
    {
      $this->out('Problem getting props...');
      $this->out($e->getMessage());
      return false;
    }

    return $rows;
  }

}

JApplicationCli::getInstance('Uploadimagestocdn')->execute();
