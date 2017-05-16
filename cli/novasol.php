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

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

// Import our base real estate cli bit
jimport('frenchconnections.cli.import');
jimport('joomla.filesystem.folder');
// Import our base real estate cli bit
jimport('frenchconnections.cli.import');


class Novasol extends Import
{

  public $api_key = 'yII1NTvYnWpLQAK7D9X1G8j42f6LaS';


  /**
   * Entry point for the script
   *
   * @return  void
   *
   * @since   2.5
   */
  public function doExecute()
  {
    // Add the classification table so we can get the location details
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_classification/tables');
    JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/tables');

    // Add the realestate property models
    JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_rental/models');
    $tariffsTable = JTable::getInstance($type = 'Tariffs', $prefix = 'RentalTable');

    define('COM_IMAGE_BASE', JPATH_ROOT . '/images/property/');

    // Set a reasonable expiry date...
    $expiry_date = JFactory::getDate('+7 day')->calendar('Y-m-d');

    $date = JFactory::getDate()->calendar('Y-m-d');

    // Get DB instance
    $db = JFactory::getDbo();

    $user = JFactory::getUser('novasol')->id;

    $this->out('About to get property list...');

    // 1. Schedule a job to generate a batch file on the novasol server - this would be to pull out availability and tariffs
    // 2. Schedule a job for an hour later to process the above batch file (or to process a ping)
    // 3. Use this cli to periodically import any new properties

    // $properties = $this->getData('https://safe.novasol.com/api/batches?country=250&company=NOV&season=2017&replyTo=http://asdasd.co.uk', $this->api_key);
    // $properties = $this->getData('https://safe.novasol.com/api/batches/319101494945383051253', $this->api_key);
    // $properties = $this->getData('https://safe.novasol.com/api/translate?salesmarket=826', $this->api_key);

    // Get and parse out the feed
    $props = $this->parseFeed('http://dev.frenchconnections.co.uk/cli/novasol/products.xml', 'products');

    var_dump($props);
  }

  // Wrapper function to get the feed data via CURL
  public function getData($uri = '', $api_key = '')
  {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Key: ' . $api_key));
    curl_setopt($ch, CURLOPT_URL, $uri);
    $result = curl_exec($ch);

    curl_close($ch);

    return $result;
  }
}

JApplicationCli::getInstance('Novasol')->execute();
