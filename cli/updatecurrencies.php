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

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
 */
class UpdateCurrenciesCron extends JApplicationCli
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
    $baseRatesArr = array();

    $conversionsArr = array('EUR' => '', 'GBP' => '', 'USD' => '');

    //This is a PHP(4/5) script example on how eurofxref-daily.xml can be parsed
    //Read eurofxref-daily.xml file in memory 
    //For this command you will need the config 
    //option allow_url_fopen=On (default)
    $XMLContent = file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
    //the file is updated daily between 2.15 p.m. and 3.00 p.m. CET

    foreach ($XMLContent as $line)
    {
      if (preg_match("/currency='([[:alpha:]]+)'/", $line, $currencyCode))
      {
        if (preg_match("/rate='([[:graph:]]+)'/", $line, $rate))
        {
          $baseRatesArr[$currencyCode[1]] = $rate[1];
        }
      }
    }


    // The value of 1 EUR against the GBP (~ 0.7)
    $conversionsArr['GBP'] = (float) $baseRatesArr['GBP'];

    // Euro rate is the reciprocal of GBP (~ 1 / 0.7)
    $conversionsArr['EUR'] = 1 / $baseRatesArr['GBP'];

    // The USD rate is the value of 1 GBP converted to EUR multiplied by the USD EUR rate
    $conversionsArr['USD'] = ( 1 / $baseRatesArr['GBP'] ) * $baseRatesArr['USD'];

    $db = JFactory::getDbo();
    $db->transactionStart();

    $query = $db->getQuery(true);

    foreach ($conversionsArr as $currency => $rate)
    {
      $query->Clear();

      $query->update($db->quoteName('#__currency_conversion', 'a'))
              ->set('a.exchange_rate = ' . $rate)
              ->where('a.currency = ' . $db->quote($currency));

      $db->setQuery($query);

      try
      {
        $db->execute();
      }
      catch (Exception $e)
      {
        // TO DO - Email exception?
        $db->transactionRollback();
      }
    }

    $db->transactionCommit();

    $this->out('Currency exchange rates updated.');
  }

}

JApplicationCli::getInstance('UpdateCurrenciesCron')->execute();
