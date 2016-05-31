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

require_once JPATH_BASE . '/administrator/components/com_fcadmin/models/noavailability.php';
require_once JPATH_BASE . '/administrator/components/com_notes/models/note.php';

// Configure error reporting to maximum for CLI output.
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class UpdateFromPriceCron extends JApplicationCli
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

    $date = JHtml::_('date', 'now', 'Y-m-d', 'Europe/London');

    // TO DO - Pull out current exchange rate and use that

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('min(tariff) * 0.7032')
            ->from($db->quoteName('#__tariffs', 't'))
            ->where($db->quoteName('t.unit_id') . ' = ' . $db->quoteName('b.id'))
            ->where($db->quoteName('end_date') . ' > ' . $db->quote($date));

    $euro_sub_query = $query->__toString();

    // Clear the select field
    $query->clear('select');

    // And do the same query with a max instead of min
    $query->select('max(tariff) * 0.7032');

    // Set the query to string var
    $euro_max_sub_query = $query->__toString();

    // Clear the query
    $query->clear();

    $query->select('min(tariff)')
            ->from($db->quoteName('#__tariffs', 't'))
            ->where($db->quoteName('t.unit_id') . ' = ' . $db->quoteName('b.id'))
            ->where($db->quoteName('end_date') . ' > ' . $db->quote($date));

    $sterling_sub_query = $query->__toString();

    $query->clear('select');

    $query->select('max(tariff)');

    $sterling_max_sub_query = $query->__toString();

    $query->clear();

    $query->select(
      'b.id, CASE WHEN d.base_currency = \'EUR\' THEN (' . $euro_sub_query . ') ELSE (' . $sterling_sub_query . ') END as from_price,
    CASE WHEN d.base_currency = \'EUR\' THEN (' . $euro_max_sub_query . ') ELSE (' . $sterling_max_sub_query . ') END as to_price'
      )
            ->from($db->quoteName('#__property','a'))
            ->join('inner', $db->quoteName('#__unit', 'b') . ' on ' . $db->quoteName('b.property_id') . ' = ' . $db->quoteName('a.id'))->join('inner', $db->quoteName('#__property_versions', 'c') . ' on ' . $db->quoteName('c.property_id') . ' = ' . $db->quoteName('a.id'))
            ->join('inner', $db->quoteName('#__unit_versions', 'd') . ' on ' . $db->quoteName('d.unit_id') . ' = ' . $db->quoteName('b.id'))
            ->where('c.review = 0')
            ->where('b.published = 1')
            ->where('d.review = 0');

    $select = $query->__toString();

    $query->Clear();

    $query->update($db->quoteName('#__unit', 'u'))
            ->join('left', '( ' . $select . ') up ON u.id = up.id')
            ->set('u.from_price = up.from_price')
            ->set('u.to_price = up.to_price');

    $db->setQuery($query);

    try
    {
      $db->execute();
    }
    catch (Exception $e)
    {
      print_r();
    }

    $this->out('From price update done.');
  }



}

JApplicationCli::getInstance('UpdateFromPriceCron')->execute();
