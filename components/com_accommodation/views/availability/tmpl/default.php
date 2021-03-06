<div class="container">
  <h3 class="page-header"> 
    <?php echo $this->document->title; ?>
  </h3>

  <div class="row">
    <div class="col-sm-6 col-md-5 col-lg-6">
      <div>
        <h4><?php echo JText::_('COM_ACCOMMODATION_AVAILABILITY_KEY') ?></h4>
        <table class="table table-condensed availability-key">
          <thead> 
            <tr>
              <th class="available">&nbsp;</th>
              <th><?php echo JText::_('COM_ACCOMMODATION_AVAILABILITY_KEY_AVAILABLE') ?></th>
              <th class="unavailable">&nbsp;</th>
              <th><?php echo JText::_('COM_ACCOMMODATION_AVAILABILITY_KEY_UNAVAILABLE') ?></th>
            </tr>
          </thead>
        </table>    
      </div>
      <div class="sidescroll-nextprev clearfix">
        <!-- 
          Inline style block to correct iOS sizing issue - PLEASE DO NOT REMOVE
          http://stackoverflow.com/questions/23083462/how-to-get-an-iframe-to-be-responsive-in-ios-safari
          https://www.frenchconnections.co.uk/administrator/index.php?option=com_tickets&task=ticket.edit&id=908
        -->
        <div class="overthrow sidescroll" style="width:1px;min-width:100%">
          <?php echo $this->availability; ?>
        </div>
      </div>

    </div>
    <div class="col-sm-6 col-md-7 col-lg-6">
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
    </div>
  </div>
</div>

