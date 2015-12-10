<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

$lang = JFactory::getLanguage();
$lang->load('com_accommodation', JPATH_SITE, null, false, true);
$lang->load('com_shortlist', JPATH_SITE, null, false, true);

if (JDEBUG)
{
    $_PROFILER = JProfiler::getInstance('Application');
}

JDEBUG ? $_PROFILER->mark('Start process search results template') : null;

$user = JFactory::getApplication();

$ordering = 'order_' . $this->state->get('list.sort_column') . '_' . $this->state->get('list.direction');

$sortFields = $this->getSortFields();
$s_kwds = $this->state->get('list.searchterm', '');

// The layout for the anchor based navigation on the property listing
$search_layout = new JLayoutFile('search_one', $basePath = JPATH_SITE . '/components/com_fcsearch/layouts');
$search_data = new stdClass;
$search_data->searchterm = UCFirst(JStringNormalise::toSpaceSeparated($this->state->get('list.searchterm')));
$search_data->bedrooms = $this->state->get('list.bedrooms');
$search_data->occupancy = $this->state->get('list.occupancy');
$search_data->arrival = ($this->state->get('list.arrival', '')) ? JFactory::getDate($this->state->get('list.arrival'))->calendar('d-m-Y') : '';
$search_data->departure = ($this->state->get('list.departure', '')) ? JFactory::getDate($this->state->get('list.departure'))->calendar('d-m-Y') : '';
$uri = JUri::getInstance()->toString();
$offers = ($this->state->get('list.offers')) ? true : false;
$lwl = ($this->state->get('list.lwl')) ? true : false;
$ItemID = SearchHelper::getItemid(array('component', 'com_fcsearch'));

?>

<form class="form-inline" id="property-search" action="<?php echo JRoute::_('index.php?option=com_fcsearch&lang=en&Itemid=' . $ItemID . '&s_kwds=' . $s_kwds) ?>" method="POST">
  <h1 class="small-h1 page-header">
    <?php echo $this->escape(str_replace(' - French Connections', '', $this->document->title)); ?>
  </h1>

  <div class="well well-sm well-light-blue clearfix form-inline">  
    <?php echo $search_layout->render($search_data); ?>
  </div>

  <?php $offer_filter = JHtml::_('refine.removeQueryFilter', (bool) $offers, 'offers', 'COM_FCSEARCH_SEARCH_FILTER_OFFERS', $uri); ?>
  <?php $lwl_filter = JHtml::_('refine.removeQueryFilter', (bool) $lwl, 'lwl', 'COM_FCSEARCH_SEARCH_FILTER_LWL', $uri); ?>
  <?php $attribute_filter = JHtml::_('refine.removeAttributeFilters', $this->attribute_options, $uri, ''); ?>
  <?php $property_filter = JHtml::_('refine.removeAttributeFilters', array('options' => $this->property_options), $uri, 'property_'); ?>
  <?php $accommodation_filter = JHtml::_('refine.removeAttributeFilters', array('options' => $this->accommodation_options), $uri, 'accommodation_'); ?>
  <div class="row">
    <div class="col-xs-12">
      <p class="clearfix">
      <span class="pull-right">
        <a href="<?php echo JUri::getInstance()->toString() . '#refine' ?>" class="btn btn-default visible-sm-inline-block visible-xs-inline-block">  
          <span class="glyphicon glyphicon-filter"></span>
          <?php echo JText::_('COM_FCSEARCH_FILTER_RESULTS'); ?>
        </a>    

      </span>

      <?php if (!empty($attribute_filter) || !empty($property_filter) || !empty($accommodation_filter) || !empty($offer_filter) || !empty($lwl_filter)) : ?>
          <span class="pull-left">
          <?php echo JText::_('COM_FCSEARCH_FILTER_APPLIED'); ?>
          <?php echo $attribute_filter, $property_filter, $accommodation_filter, $offer_filter, $lwl_filter; ?>
          </span>
        
      <?php endif; ?> 
      </p>
    </div>
  </div>

  <div class="row">
    <div class="tab-content col-lg-9 col-md-9">
      <div class="clearfix">
        <p class="pull-left">
          <?php echo $this->pagination->getResultsCounter(); ?> 
        </p>
        <p class="pull-right">
          <label for="sort_by" class="sr-only">
            <?php echo JText::_('COM_FCSEARCH_SEARCH_SORT_BY'); ?>
          </label>
          <select id="sort_by" class="form-control sort-order" name="order">
            <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $ordering); ?>
          </select>
        </p>
      </div>

      <div class="tab-pane active" id="list">
        <?php if (count($this->results) > 0) : ?>
            <div class="search-results list-unstyled" data-results='<?php json_encode($this->results) ?>' style="margin-top:0">
              <?php
              JDEBUG ? $_PROFILER->mark('Start process individual results (*10)') : null;

              for ($i = 0, $n = count($this->results); $i < $n; $i++)
              {
                  $this->result = &$this->results[$i];
                  if (!empty($this->result->id))
                  {
                      echo $this->loadTemplate('result');
                  }
              }
              JDEBUG ? $_PROFILER->mark('End process individual results (*10)') : null;
              ?>
            </div>
        <?php else: ?>
            <p class='lead'>
              <strong><?php echo JText::_('COM_FCSEARCH_SEARCH_NO_RESULTS_HEADING'); ?></strong>
            </p>
            <p><?php echo JText::_('COM_FCSEARCH_SEARCH_NO_RESULTS_BODY'); ?></p>
            <?php
            // Load the most popular search module 
            $module = JModuleHelper::getModule('mod_popular_search');
            echo JModuleHelper::renderModule($module);
            ?>
        <?php endif; ?> 
      </div>
      <div class="tab-pane row" id="mapsearch">
        <div class="col-lg-3 col-md-3 col-sm-4">
          <div id="target">Loading...</div>
          <script id="template" type="x-tmpl-mustache">
            <div class='map-search-results'>
            {{ #. }} 
            <div class='map-search-result'>
            <h4><a href={{ url }}>{{ unitTitle }}</a></h4>
            <p>
            <a href={{ url }}>
            <img class='img-responsive' src={{ thumbnail }} />
            </a>
            </p>
            <p class='small'>{{{ tagline }}}</p>
            </div>
            {{ /. }}
            </div>
          </script>
        </div>
        <div class="col-lg-9 col-md-9 col-sm-8">
          <div id="map_canvas"></div>
        </div>
      </div>
      <?php echo $this->pagination->getPagesLinks(); ?>

      <h2><?php echo $this->escape(($this->localinfo->title)); ?></h2>
      <?php echo ($this->seo_copy) ? $this->seo_copy : $this->localinfo->description; ?>
    </div>
    <div class="col-lg-3 col-md-3 refine-search">
      <?php
      JDEBUG ? $_PROFILER->mark('Start process refine') : null;
      echo $this->loadTemplate('refine');
      JDEBUG ? $_PROFILER->mark('End process refine') : null;
      ?>
    </div>
  </div>
</form>
<?php JDEBUG ? $_PROFILER->mark('End process search results template') : null; ?>
