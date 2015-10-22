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
$uri = JUri::getInstance()->toString(array('user', 'pass', 'host', 'port', 'path', 'query', 'fragment'));
$offers = ($this->state->get('list.offers')) ? true : false;
$lwl = ($this->state->get('list.lwl')) ? true : false;
$ItemID = SearchHelper::getItemid(array('component', 'com_fcsearch'));

$latitude = $this->state->get('search.latitude', '');
$longitude = $this->state->get('search.longitude', '');

// Prepare the pagination string.  Results X - Y of Z
// $start = (int) $this->pagination->get('limitstart') + 1;
// $total = (int) $this->pagination->get('total');
// $limit = (int) $this->pagination->get('limit') * $this->pagination->pagesTotal;
// $limit = (int) ($limit > $total ? $total : $limit);
// $pages = JText::sprintf('COM_FCSEARCH_TOTAL_PROPERTIES_FOUND', $total);
?>

<form class="form-inline" id="property-search" action="<?php echo JRoute::_('index.php?option=com_fcsearch&lang=en&Itemid=' . $ItemID . '&s_kwds=' . $s_kwds) ?>" method="POST">
    <h1 class="small-h1 page-header">
        <?php echo $this->escape(str_replace(' - French Connections', '', $this->document->title)); ?>
    </h1>


    <?php $offer_filter = JHtml::_('refine.removeOffersFilter', (bool) $offers); ?>
    <?php $lwl_filter = JHtml::_('refine.removeLWLFilter', (bool) $lwl); ?>
    <?php $attribute_filter = JHtml::_('refine.removeAttributeFilters', $this->attribute_options, $uri); ?>
    <?php $property_filter = JHtml::_('refine.removeTypeFilters', $this->property_options, $uri, 'property_'); ?>
    <?php $accommodation_filter = JHtml::_('refine.removeTypeFilters', $this->accommodation_options, $uri, 'accommodation_'); ?>

    <div class="row">
        <div class="tab-content col-lg-9 col-md-9 col-md-push-3 col-lg-push-3">

            <div class="tab-pane active" id="list">
                <div class="well well-sm well-light-blue clearfix form-inline">  
                    <?php echo $search_layout->render($search_data); ?>
                </div>

                <div class="row">
                    <div class="col-lg-8 col-xs-12 col-md-8 col-sm-8">
                        <p class="pull-left" style='line-height: 28px;'>
                            <?php echo $this->pagination->getResultsCounter(); ?>
                        </p>
                    </div>
                    <div class="col-lg-4 col-xs-6 col-md-4 col-sm-4">
                        <label for="sort_by" class="sr-only">
                            <?php echo JText::_('COM_FCSEARCH_SEARCH_SORT_BY'); ?>
                        </label>
                        <select id="sort_by" class="form-control" name="order">
                            <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $ordering); ?>
                        </select>
                    </div>
                    <div class="visible-sm-inline-block visible-xs-inline-block col-xs-6">
                        <p class=" pull-right">
                            <a href="<?php echo JUri::getInstance()->toString() . '#refine' ?>" class="btn btn-default">  
                                <span class="glyphicon glyphicon-filter"></span>
                                <?php echo JText::_('COM_FCSEARCH_FILTER_RESULTS'); ?>
                            </a>
                        </p>
                    </div>
                </div>   
                <?php if (!empty($attribute_filter) || !empty($property_filter) || !empty($accommodation_filter) || !empty($offer_filter) || !empty($lwl_filter)) : ?>
                    <?php echo JText::_('COM_FCSEARCH_FILTER_APPLIED'); ?>
                    <?php echo $attribute_filter, $property_filter, $accommodation_filter, $offer_filter, $lwl_filter; ?>
                    <hr />
                <?php endif; ?>
                <?php if (count($this->results) > 0) : ?>

                    <div class="search-results list-unstyled clear" data-results='<?php json_encode($this->results) ?>'>
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
                <?php echo $this->pagination->getPagesLinks(); ?>

            </div>
            <div class="tab-pane" id="mapsearch">

                <div id="target">Loading...</div>
                <script id="template" type="x-tmpl-mustache">
                    <div class='map-search-results monkey'>
                    {{ #. }}
                    <div class='map-search-result'>
                    <h4> {{ unitTitle }} 
                    <small> {{ propertyType }} </small>
                    </h4>
                    </div>
                    {{ /. }}
                    </div>
                </script>

                <div id="search-map"></div>

            </div>
            <h2><?php echo $this->escape(($this->localinfo->title)); ?></h2>
            <?php echo ($this->seo_copy) ? $this->seo_copy : $this->localinfo->description; ?>
        </div>
        <div class="col-lg-3 col-md-3 col-lg-pull-9 col-md-pull-9 refine-search">
            <ul class="nav nav-stacked nav-pills" id="map-search-tab">
                <li>
                    <a href="#list" data-toggle="tab" class='btn btn-default'>
                        <i class="glyphicon glyphicon-arrow-left"></i>
                        <?php echo JText::_('COM_FCSEARCH_BACK_TO_LIST') ?>
                    </a>
                </li>
                <li>
                    <a href="#mapsearch" data-toggle="tab">
                        <img class="img-responsive" src="<?php echo '//maps.googleapis.com/maps/api/staticmap?center=' . $latitude . ',' . $longitude . '&size=320x250&zoom=7&scale=2key=AIzaSyAvIkhf8zAayRncfQ9weXMdnYModMHGDPE' ?>" />
                    </a>
                </li>
            </ul>

            <?php
            JDEBUG ? $_PROFILER->mark('Start process refine') : null;
            echo $this->loadTemplate('refine');
            JDEBUG ? $_PROFILER->mark('End process refine') : null;
            ?>
        </div>
    </div>
</form>
<?php JDEBUG ? $_PROFILER->mark('End process search results template') : null; ?>

<script>

    // Some work to do here on clearing out the map search results if going back to list view
    // and vice versa.
    // Also need to integrate the map here? as well

    jQuery('#map-search-tab a[data-toggle="tab"]').on('show.bs.tab', function (e) {

        jQuery(e.target).toggle();
      jQuery(e.relatedTarget).toggle();

      // Store the selected tab #ref in local storage, IE8+
      var selectedTab = jQuery(e.target).attr('href');
      
      // Set the local storage value so we 'remember' where the user was
      localStorage['selectedTab'] = selectedTab; 
      

      var template = jQuery('#template').html();
      var data = [];
      jQuery('.search-result').each(function () {
        var tmp = jQuery(this).data();
        data = data.concat(tmp);
      })

      Mustache.parse(template); // optional, speeds up future uses

      var rendered = Mustache.render(template, data);
      jQuery('#target').html(rendered);


    });

</script>
