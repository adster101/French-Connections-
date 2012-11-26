<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>
<div class="">
  <form action="<?php echo JRoute::_('index.php'); ?>" method="GET" class="form-vertical">
    <div class="well clearfix">
      <h4 class="bottom">Search</h4>
      <p class="bottom">Enter your destination in the box below to search through over 2000 properties.</p>

      <label class="accessibility" for="s_kwds">Search</label>
      <input id="s_kwds" class="input-medium typeahead" type="text" name="s_kwds" autocomplete="Off" />

      <label for="fArrivalDate">Arrival</label> 
      <input id="fArrivalDate" class="input-small" type="text" name="start_date" />

      <label for="fDepartureDate">Departure</label> 
      <input id="fDepartureDate" class="input-small" type="text" name="end_date" />

      <label for="search_sleeps">Sleeps</label>
      <select id="search_sleeps" class="input-small" name="min_occupants">
        <option value="">Select...</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
      </select>
      <p>
        <a id="genericSearchSubmit" class="btn btn-large btn-primary pull-right" href="#">Search</a>
      </p>

      <input type="hidden" name="s_acctype" value="24,25" /> <input type="hidden" name="s_tp" value="std" />
    </div>
  </form>
</div>

<script>
  jQuery(".typeahead").typeahead({
  source: function (query, process) {
    jQuery.get('/autocomplete', { q: query }, function (data) {
      process(data)
    })
  }
})
</script>

