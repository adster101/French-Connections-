<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_footer
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>
<script type="text/javascript">
  var myDate = new Date();
  var myStamp =
          "" + myDate.getDate() + myDate.getMonth() + myDate.getFullYear() + myDate.getHours() + myDate.getMinutes();
  document.write('<script type="text/javascript" src="https://ajaxgeo.cartrawler.com/cartrawlerabe/abe/js/abeSVNInfo.js?' + myStamp + '"><\/script>');</script>
<script type="text/javascript">
  document.write('<script type="text/javascript" src="https://ajaxgeo.cartrawler.com/cartrawlerabe/abe/js/ct_abe.js?' + CARTRAWLER.SVNInfo.revision + '"><\/script>');
</script>

<div id="abe_ABE"><noscript>YOUR BROWSER DOES NOT SUPPORT JAVASCRIPT</noscript></div>

<script type="text/javascript">
<!--
  var ctOTAEngine = new CT_OTA_Engine("ABE");
  ctOTAEngine.setDefaultURL("https://ajaxgeo.cartrawler.com/cartrawlerabe/");
  ctOTAEngine.setURL("/libraries/frenchconnections/misc/otaproxygeo.php"); // Provided by CarTrawler
  ctOTAEngine.setTarget("Production"); // Target for the engine
  ctOTAEngine.setClientID("387737"); // Provided by CarTrawler
  ctOTAEngine.addCurrency(); // adds list of all available currencies
  ctOTAEngine.setCurrency("EUR"); // default Currency
  ctOTAEngine.setDefaultLanguage("EN"); // Default Language
  ctOTAEngine.enableAutoSuggest({style: "lightgray", flag: true});
  ctOTAEngine.enableNewCalendar({style: "blue", daterange: true});
// ----- optional ctOTAEngine methods to proceed this line -----
  ctOTAEngine.setWebsiteConditionsURL("http://www.cartrawler.com/bookingengine-conditions.html");
  ctOTAEngine.setErrorReportUrl("https://www.cartrawler.com/ajaxerror.asp");
  ctOTAEngine.setCountryID("GB"); // Default Country
  ctOTAEngine.displayBookEngine();
// ----- optional custom events to proceed this line -----
// -->
</script>
