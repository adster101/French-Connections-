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

class Novasol extends Import
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
  }
}

JApplicationCli::getInstance('Novasol')->execute();
