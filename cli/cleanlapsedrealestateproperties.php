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
if (file_exists(dirname(__DIR__) . '/defines.php')) {
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

/**
 * Cron job to trash expired cache data.
 *
 * @since  2.5
 */
class CleanLapsedRealEstateAffiliateProperties extends JApplicationCli
{

  var $image_path = '/var/www/html/images/property/';

  var $users_to_clean = array(10154, 9773, 8290, 7931,10217);

	/**
	 * Entry point for the script
	 *
	 * @return  void
	 *
	 * @since   2.5
	 */
	public function doExecute()
	{
    $total = 0;

    foreach ($this->users_to_clean as $user_id)
    {
      $props = $this->_getProps($user_id);

      $this->out('Deleteing ' . count($props) . ' expired listings for ' . $user_id);

      // Process the renewal reminders
      foreach ($props as $k => $v)
      {

        // Delete all trace of this listing...
        // 1. Start transaction
        // 2. Delete id from #__realestate_property
        // 3. Delete from #__realestate_property_images_library
        // 4. Delete from #__realestate_property_versions
        // 5. Delete from #__property_views

        $db = JFactory::getDBO();

        try
        {

          // Start transaction
          $db->transactionStart();

          // Delete from the property table
          $query = $db->getQuery(true);
          $query->delete('`#__realestate_property`');
          $query->where(' id = ' . (int) $v->PRN);
          $this->delete($db, $query);

          // Delete from the property images library table.
          $query = $db->getQuery(true);
          $query->delete('`#__realestate_property_images_library`');
          $query->where(' realestate_property_id = ' . (int) $v->PRN);
          $this->delete($db, $query);

          // Delete from the property versions table.
          $query = $db->getQuery(true);
          $query->delete('`#__realestate_property_versions`');
          $query->where(' realestate_property_id = ' . (int) $v->PRN);
          $this->delete($db, $query);

          // Delete from the property views table
          $query = $db->getQuery(true);
          $query->delete('`#__property_views`');
          $query->where(' property_id = ' . (int) $v->PRN);
          $this->delete($db, $query);

          $f = $this->image_path . $v->PRN;

          if (file_exists($f))
          {
            $size = $this->dir_size($f);
            $total += $size;
          }

          if (file_exists($f))
          {
            // Remove the images for this listing
            if (!$this->deleteImages($f))
            {
              $this->out('Error deleting directory ' . $f);
            }
          }

          $db->transactionCommit();

        }
        catch (Exception $e)
        {
          $db->transactionRollback();
          $this->out($e->getMessage());
        }
      }

      $this->out('Deleted listings and recovered ' . $this->format_size($total));
    }
	}

  /*
   * Get a list of properties that have expired
   *
   */

  private function _getProps($user = '', $expired = true)
  {

    //$this->out('Getting props...');

    $db = JFactory::getDBO();
    /**
     * Get the date
     */

    $date = JFactory::getDate();
    //$date = JFactory::getDate();

    $query = $db->getQuery(true);

    $query->select('
      a.id PRN, a.expiry_date'
    );

    // Select from the property table
    $query->from('#__realestate_property a');

    // Join the unit table
    //$query->leftJoin('#__unit b ON b.property_id = a.id');

    // Live properties, that are published
    $query->where('expiry_date < ' . $db->quote($date->calendar('Y-m-d')));
    $query->where('a.published = 1');

    //$query->where('b.published = 1');
    //$query->where('a.created_by not in (10217, 7931, 10154, 8290, 9773)');
    $query->where('a.created_by = ' . (int) $user);

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

  // Delete the images for the expired property

  public function deleteImages($directory) {

    $iterator = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);

    $files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

    foreach($files as $file) {

      // Check if the file is a directory
      if ($file->isDir())
      {
        rmdir($file->getRealPath());
      }
      else
      {
        unlink($file->getRealPath());
      }
    }

    // Finally remove the actual directory
    return rmdir($directory);
  }

  public function delete($db, $query) {

    // Execute query
    $db->setQuery($query);

    try
    {
      $db->execute();
    }
    catch (RuntimeException $e)
    {
      throw new Exception($e->getMessage());
    }

    return true;
  }

  function dir_size($directory) {
      $size = 0;
      foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){
          $size += $file->getSize();
      }
      return $size;
  }

  function format_size($size) {
    $mod = 1024;
    $units = explode(' ','B KB MB GB TB PB');
    for ($i = 0; $size > $mod; $i++) {
        $size /= $mod;
    }
    return round($size, 2) . ' ' . $units[$i];
  }
}

JApplicationCli::getInstance('CleanLapsedRealEstateAffiliateProperties')->execute();
