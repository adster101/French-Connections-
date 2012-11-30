<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
?>

<form id="finder-search" action="" method="get" class="form-vertical">
  <div class="row-fluid">
    <div class="well well-small clearfix">
    <div class="span4">
      <label for="q">
        <?php echo JText::_('COM_FINDER_SEARCH_TERMS'); ?>
      </label>
      <input type="text" name="q" id="q" size="30" value="<?php ?>" class="inputbox span12" />
    </div>
    <div class="span1">
      <label for="search_sleeps">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_OCCUPANCY') ?>
      </label> 
      <select id="search_sleeps" class="span12" name="occupancy">
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
    <div class="span1">
      <label for="search_bedrooms">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_BEDROOMS') ?>
      </label>
      <select id="search_bedrooms" class="span12" name="bedrooms">
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
    <div class="span2">
      <label for="search_bedrooms">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_ARRIVAL') ?>
      </label>       <input type="text" name="q" id="q" size="30" value="<?php ?>" class="inputbox span9" />


    </div>
    <div class="span2">
      <label for="search_bedrooms">
        <?php echo JText::_('COM_FCSEARCH_SEARCH_DEPARTURE') ?>
      </label>   
      <input type="text" name="q" id="q" size="30" value="<?php ?>" class="inputbox span9" />

    </div>    
   
    <div class="span2">
      <button name="Search" type="submit" class="btn btn-primary btn-large"><i class="icon-search icon-white"></i> <?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
    </div>  
      
    </div>
  </div>
  <?php
  if ($this->total == 0):
  ?>
  <div id="search-result-empty">
    <h2><?php echo JText::_('COM_FINDER_SEARCH_NO_RESULTS_HEADING'); ?></h2>
    <?php if ($app->getLanguageFilter()) : ?>
      <p><?php echo JText::sprintf('COM_FINDER_SEARCH_NO_RESULTS_BODY_MULTILANG', $this->escape($this->query->input)); ?></p>
    <?php else : ?>
      <p><?php echo JText::sprintf('COM_FINDER_SEARCH_NO_RESULTS_BODY', $this->escape($this->query->input)); ?></p>
    <?php endif; ?>
  </div>


  <?php else:
  // Prepare the pagination string.  Results X - Y of Z
  $start = (int) $this->pagination->get('limitstart') + 1;
  $total = (int) $this->pagination->get('total');
  $limit = (int) $this->pagination->get('limit') * $this->pagination->pagesTotal;
  $limit = (int) ($limit > $total ? $total : $limit);
  $pages = JText::sprintf('COM_FCSEARCH_TOTAL_PROPERTIES_FOUND', $total);
  ?>
  <div class="row-fluid">
    <div class="span9">
  <div class="search-pagination">
    <div class="pagination">
      <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
   
  </div>
    </div>
    <div class="span2">
           <?php echo $this->pagination->getResultsCounter(); ?>
    </div>
        <div class="span1">
          <?php echo $this->pagination->getLimitBox(); ?>
          
    </div>
  </div>
  <?php endif; ?>
</form>
