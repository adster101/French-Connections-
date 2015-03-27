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

require_once JPATH_BASE . '/administrator/components/com_fcadmin/models/noavailability.php';
require_once JPATH_BASE . '/administrator/components/com_notes/models/note.php';

jimport('frenchconnections.cli.crawler');

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class CrawlerCron extends JApplicationCli
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
    $app = JFactory::getApplication('site');
    $lang = JFactory::getLanguage();

    $status = array();

    // Get the debug setting
    $debug = (bool) $app->getCfg('debug');
    define('DEBUG', $debug);

    $props = $this->_getProps();

    foreach ($props as $prop)
    {
      $crawler = new Crawler($prop->website);
      $crawler->crawl($prop->website);

      if ($crawler->found)
      {
        $status[$prop->id] = true;
      }
      else
      {
        $status[$prop->id] = false;
      }

      print_r($status);
    }
  }

  private function _getProps()
  {


    $db = JFactory::getDBO();
    /**
     * Get the date now
     */
    $date = JHtml::_('date', 'now', 'Y-m-d');

    $query = $db->getQuery(true);

    $query->select('
    	a.id,
      a.expiry_date, 
      b.website'
    );

    $query->from('#__property a');
    $query->leftJoin($db->quoteName('#__property_versions', 'b') . ' on a.id = b.property_id');
    $query->where('b.review = 0');
    $query->where('b.website <> \'\'');
    $query->where($db->quoteName('expiry_date') . ' >= ' . $db->quote($date));
    $query->order('expiry_date');

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

JApplicationCli::getInstance('CrawlerCron')->execute();
