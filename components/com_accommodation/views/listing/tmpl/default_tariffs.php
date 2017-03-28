<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
 <?php
      // Shortlist button thingy
      $tariffs = new JLayoutFile('frenchconnections.accommodation.tariffs');
      $displayData = new StdClass;
      $displayData->tariffs = $this->tariffs;
      $displayData->base_currency = $this->item->base_currency;
      $displayData->exchange_rate_eur = $this->item->exchange_rate_eur;
      $displayData->exchange_rate_usd = $this->item->exchange_rate_usd;
      $displayData->tariffs_based_on = $this->item->tariffs_based_on;
      echo $tariffs->render($displayData);
      ?>