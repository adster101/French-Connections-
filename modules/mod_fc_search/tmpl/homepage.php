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
<form id="property-search" action="<?php echo JRoute::_(JURI::base() . 'index.php?option=com_fcsearch&view=search&lang=en') ?>" method="POST" class="form-vertical">
  <div class="well clearfix">
    <h4 class="bottom"><?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_SEARCH') ?></h4>

    <label for="s_kwds">
      <?php echo JText::_('COM_FCSEARCH_SEARCH_DESTINATION') ?>
    </label>
    <input id="s_kwds" class="input-medium typeahead" type="text" name="s_kwds" autocomplete="Off" />

    <!--<label for="fArrivalDate">Arrival</label> 
    <input id="fArrivalDate" class="input-small" type="text" name="start_date" />

    <label for="fDepartureDate">Departure</label> 
    <input id="fDepartureDate" class="input-small" type="text" name="end_date" />-->
    <div class="row-fluid">
    <div class="span6">
      <label for="search_sleeps">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') ?>
      </label>
      <select id="search_sleeps" class="input-mini" name="occupancy">
        <option value="">...</option>
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
    </div>
    <div class="span6">
      <label for="search_bedrooms">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') ?>
      </label>
      <select id="search_bedrooms" class="input-mini" name="bedrooms">
        <option value="">...</option>
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
    </div>
    </div>
    <button id="property-search-button" class="btn btn-large btn-primary pull-right" href="#">
      <i class="icon-search icon-white"> </i>
      <?php echo JText::_('COM_FCSEARCH_SEARCH') ?>
    </button>
  </div>
</form>
<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Modal header</h3>
  </div>
  <div class="modal-body">
    <p>One fine body…</p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    <button class="btn btn-primary">Save changes</button>
  </div>
</div>

<script charset="utf-8">
   
  jQuery('#property-search-button').click(function(event){
    
    // val is the 'active' suggestion populated by typeahead
    // e.g. the option chosen should be the last active one
    var val = jQuery(".typeahead.dropdown-menu").find('.active').attr('data-value');
    
    // The value contained in the typeahead field
    var chosen = jQuery(".typeahead").attr('value');
    
    // The two should match as we want to ensure user enters destination
    if (val !== chosen) {
      jQuery('#myModal').modal();
      return false
    }
    
    // Form checks out, looks like the user chose something from the suggestions
    // Strip the string to make it like classifications table alias
    var query = stripVowelAccent(chosen);   

    jQuery('form#property-search').attr('action', '/index.php?option=com_fcsearch&view=search&lang=en&q='+query);
    
    jQuery('form#property-search').submit();
    
    event.preventDefault();  
    return false;
    
  })
  
  jQuery(".typeahead").typeahead({
     
    source: function (query, process) {
      jQuery.get(
      '/index.php?option=com_fcsearch&task=suggestions.display&format=json&tmpl=component&lang=en', 
      { 
        q: query,
        items: 10
      }, 
      function (data) {
        process(data);
      }
    )
    }
  })

  // Function removes and replaces all French accented characters with non accented characters
  // as well as removing spurious characters and replacing spaces with dashes...innit!
  function stripVowelAccent(str) {

    var s=str;
    var rExps=[ 
        /[\xC0-\xC2]/g, 
      /[\xE0-\xE2]/g,
      /[\xC8-\xCA]/g,
      /[\xE8-\xEB]/g,
      /[\xCC-\xCE]/g,
      /[\xEC-\xEE]/g,
      /[\xD2-\xD4]/g,
      /[\xF2-\xF4]/g,
      /[\xD9-\xDB]/g,
      /[\xF9-\xFB]/g
    ];

    var repChar=['A','a','E','e','I','i','O','o','U','u'];

    for(var i=0; i<rExps.length; i++) {
      s=s.replace(rExps[i],repChar[i]);
    }
    
    // Replace braces with spaces
    s=s.replace(/[()]/g,' ');
    
    // Replace apostrophe with space
    s=s.replace(/[\']/g,' ');
    
    // Trim any trailing space
    s=s.trim();
    
    // Remove any duplicate whitespace, and ensure all characters are alphanumeric
    s=s.replace(/(\s|[^A-Za-z0-9\-])+/g,'-');

  
    
    s=s.toLowerCase();

    return s;
  }
</script>

