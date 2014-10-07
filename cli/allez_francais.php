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
class AllezFrancais extends JApplicationCli
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
    try {

      // First things first, get and parse out the feed 
      $props = $this->parseFeed('http://www.allez-francais.com/allez-francais.xml');

      // Add the realestate property models/table
      JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_realestate/models');

      JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_realestate/tables');

      // Get an instance of the propertyversions table and set the primary key 
      $realestate_property_version = JTable::getInstance('PropertyVersions', 'RealestateTable');
      $realestate_property_version->set('_tbl_keys', array('agency_reference'));

      foreach ($props->properties as $prop)
      {

        // $ref = $this->getProperty($prop->agency_reference);
        
        if (!$realestate_property_version->load($prop->agency_reference)) {
          
        }
        
        if (empty($realestate_property_version->id))
        {
          // Probably more performant to do an insert in a separate function wrapped in a transaction
          $realestate_property = JTable::getInstance('Property', 'RealestateTable');

          // This property doesn't exist in the Versions table so let's add it
          $realestate_property->expiry_date = JFactory::getDate('+1 week')->calendar('Y-m-d');
          $realestate_property->created_by = 1;
          $realestate_property->review = 0;
          $realestate_property->published = 1;
          
          $realestate_property->store();
          
          $realestate_property->reset();
          
          // Insert also into the property versions table - As above for insertion 
          $version = JTable::getInstance('PropertyVersions', 'RealestateTable');
          $version->set('_tbl_keys', array('id'));
          $prop->realestate_property_id = $realestate_property->id;
          $version->save($prop);
        }
        else
        {
          // Yep, we have it already!
          // $ref = $this->updateProperty($realestate_property_version->id);

        }
      }
    }
    catch (InvalidArgumentException $e) {
      $this->out(var_dump($e));
      // Set up exception email here.
    }
  }

  public function parseFeed($uri = '')
  {
    // Fetch and parse the feed.
    // Throw exception if feed not parsed/available.
    // Import the document Feed parser.
    // This might get messy when we add the Freddy Rueda feed into the mix up.
    jimport('frenchconnections.feed.document');

    // Get an instance of JFeedFactory
    $feed = new JFeedFactory;

    // Register the parser, this is the bit that seems like overkill
    $feed->registerParser('document', 'JFeedParserDocument');

    // Get and parse the feed, returns a parsed list of items.
    $data = $feed->getFeed($uri);
    

    return $data;
  }

}

JApplicationCli::getInstance('AllezFrancais')->execute();
