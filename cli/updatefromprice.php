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
jimport('clickatell.SendSMS');

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

    $date = JHtml::_('date', 'now', 'Y-m-d');

    // TO DO - Pull out current exchange rate and use that

    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('min(tariff)')
            ->from($db->quoteName('#__tariffs', 't'))
            ->where($db->quoteName('t.unit_id') . ' = ' . $db->quoteName('b.id'))
            ->where($db->quoteName('end_date') . ' > ' . $db->quote($date));

    $sub_query = $query->__toString();

    $query->clear();

    $query->select('b.id, (' . $sub_query . ') as price')
            ->from($db->quoteName('#__property','a'))
            ->join('inner', $db->quoteName('#__unit', 'b') . ' on ' . $db->quoteName('b.property_id') . ' = ' . $db->quoteName('a.id'))->join('inner', $db->quoteName('#__property_versions', 'c') . ' on ' . $db->quoteName('c.property_id') . ' = ' . $db->quoteName('a.id'))
            ->join('inner', $db->quoteName('#__unit_versions', 'd') . ' on ' . $db->quoteName('d.unit_id') . ' = ' . $db->quoteName('b.id'))
            ->where('c.review = 0')
            ->where('b.published = 1')
            ->where('d.review = 0');

    $select = $query->__toString();

    $query->clear();

    $query->update($db->quoteName('#__unit', 'u'))
            ->join('left', '( ' . $select . ') up ON u.id = up.id')
            ->set('u.from_price = up.price');

    $db->setQuery($query);

    try
    {
      $db->execute();
    }
    catch (Exception $e)
    {
      print_r($e);
    }

    $this->out('From price update done.');
  }

}

JApplicationCli::getInstance('UpdateFromPriceCron')->execute();
