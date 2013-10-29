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
   * 
   * @param type $price
   * @param type $baseCurrency
   * @param type $exchangeRate
   */
  public static function price($price = '', $baseCurrency = 438, $exchangeRate = '') {

    if ($baseCurrency == 439) {
      $exchange_rate = $this->getExchangeRate($baseCurrency);
    }
    // Default currency is GBP
    // If base currency is not GBP e.g. 439
    // then convert EUR price to GBP
  }

  /**
   * Method to get the exchange rate for the currency given
   * 
   * @param type $currencyID
   */
  public function getExchangeRate($currencyID = '') {

    // TO DO - mem cache this
    $db = JFactory::getDbo();

    $query = $db->getQuery(true);

    $query->select(exchange_rate);
    $query->from('#__currency_conversion');
    $query->where('id=' . (int) $currencyId);

    $exchange_rate = $db->loadObject();

    return $exchange_rate;
  }

}
