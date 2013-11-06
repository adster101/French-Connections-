<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/**
 * Extended Utility class for the Users component.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       2.5
 */
class JHtmlGeneral {

  /**
   * Display an image.
   *
   * @param   string  $src  The source of the image
   *
   * @return  string  A <img> element if the specified file exists, otherwise, a null string
   *
   * @since   2.5
   */
  public static function image($src) {
    $src = preg_replace('#[^A-Z0-9\-_\./]#i', '', $src);
    $file = JPATH_SITE . '/' . $src;

    jimport('joomla.filesystem.path');
    JPath::check($file);

    if (!file_exists($file)) {
      return '';
    }

    return '<img src="' . JUri::root() . $src . '" alt="" class="thumbnail" />';
  }

  /*
   * A generic make button function button
   *
   *
   */

  public static function button($btnClass = '', $task = '', $iconClass = '', $text) {

    $html = '';
    $html.='<button class="' . $btnClass . '" onclick="Joomla.submitbutton(\'' . $task . '\')">'
            . '<i class="' . $iconClass . '"></i>'
            . JText::_($text)
            . '</button>';

    return $html;
  }

  /**
   * Returns the price in GBP dependent on the base currency and exchange rate.
   * TO DO this might need to return an array of prices...
   * @param type $price
   * @param type $baseCurrency
   * @param type $exchangeRate
   */
  public static function price($price = '', $baseCurrency = 438, $exchange_rate_eur, $exchange_rate_usd) {

    $prices = array();
    $rates = JHtmlGeneral::getExchangeRates();
  

    // If the base currency is set in pounds then do nout
    if ($baseCurrency == 438) {
      $prices['GBP'] = $price;
      $prices['EUR'] = ($exchange_rate_eur > 0) ? round(($exchange_rate_eur * $price), $int = 0, $mode = PHP_ROUND_HALF_UP) : round((($rates['EUR']->exchange_rate) * $price), $int = 0, $mode = PHP_ROUND_HALF_UP);
      $prices['USD'] = ($exchange_rate_usd > 0) ? round(($exchange_rate_usd * $price), $int = 0, $mode = PHP_ROUND_HALF_UP) : round((($rates['USD']->exchange_rate) * $price), $int = 0, $mode = PHP_ROUND_HALF_UP);
    } elseif ($baseCurrency == 439) { // Base rate is in euros
      $prices['EUR'] = $price;
      $prices['GBP'] = ($exchange_rate_eur > 0) ? round(($exchange_rate_eur / $price), $int = 0, $mode = PHP_ROUND_HALF_UP) : round((($rates['GBP']->exchange_rate) / $price), $int = 0, $mode = PHP_ROUND_HALF_UP);
      // To convert into USD we need to convert from GBP
      $prices['USD'] = ($exchange_rate_usd > 0) ? round(($exchange_rate_usd / $prices['GBP']), $int = 0, $mode = PHP_ROUND_HALF_UP) : round((($rates['USD']->exchange_rate) / $prices['GBP']), $int = 0, $mode = PHP_ROUND_HALF_UP);
    }



    // And convert the price to the pound equivalent

    return $prices;
  }

  /**
   * Method to get the exchange rate for the currency given
   * 
   * @param type $currencyID
   */
  public static function getExchangeRates($currencyID = '') {

    $exchange_rate = '';

    // TO DO - *cache* this - although query should already be cached
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select('currency,exchange_rate');
    $query->from('#__currency_conversion');

    $db->setQuery($query);

    $result = $db->loadObjectList($key = 'currency');


    $exchange_rates = $result;

    return $exchange_rates;
  }

}

