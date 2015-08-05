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
jimport('frenchconnections.images.filter.interlace');

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class RentalImages extends JApplicationCli
{

  private $users_to_ignore = '9436';

  /**
   * Array to hole the 
   * 
   * @var type array
   */
  public $profiles = array('903x586', '770x580', '617x464', '408x307', '330x248', '210x120');

  /**
   * Entry point for the script
   *
   * @return  void
   *
   * @since   2.5
   */
  public function doExecute()
  {

    // Instantiate a Rackspace client.
    $client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
        'username' => 'fcadmin01',
        'apiKey' => '971715d42f3a40d3bcb42f7286477f45'
    ));

    // Obtain an Object Store service object from the client.
    $objectStoreService = $client->objectStoreService(null, 'LON');

    $container = $objectStoreService->getContainer('images');
    $handle = fopen($file_name, 'r');

    $object = $container->uploadObject($unit_id . '/' . $this->profiles[$key] . '_' . $image_file_name, $handle);
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
    $query->where('c.created_by not in (' . $db->quote($this->users_to_ignore) . ')');

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
