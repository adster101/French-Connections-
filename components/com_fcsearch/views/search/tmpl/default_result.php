<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

// Get the mime type class.
$mime = !empty($this->result->mime) ? 'mime-' . $this->result->mime : null;

// Get the base url.
$base = JURI::getInstance()->toString(array('scheme', 'host', 'port'));

// Get the route with highlighting information.
if (!empty($this->query->highlight) && empty($this->result->mime) && $this->params->get('highlight_terms', 1) && JPluginHelper::isEnabled('system', 'highlight')) {
  $route = $this->result->route . '&highlight=' . base64_encode(serialize($this->query->highlight));
} else {
  $route = '';
}


// Store the first paragraph - This should always contain something (e.g. description must start with a paragraph)
$this->result->tagline = $this->result->title;

// Strip the first paragraph so we can deal with it separately
$this->result->description = strip_tags(stripslashes($this->result->description)); // convert to plaintext

$this->result->description = substr($this->result->description, 0, strpos(wordwrap($this->result->description, 150), "\n"));

$pathway = explode('/', $this->result->path);
$route = JRoute::_('index.php?option=com_accommodation&view=property&id=' . (int) $this->result->id . '&unit_id=' . (int) $this->result->unit_id );
?>

<li>
</p>
<p class="pull-right">
  <a class="btn btn-small" href="#">
    <i class="icon-bookmark"> </i><?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_TO_FAVOURITES') ?>
  </a>
</p>
<h3 class="result-title <?php echo $mime; ?>">
  <a href="<?php echo JRoute::_($route); ?>"><?php echo $this->result->unit_title; ?></a>
  <small><?php echo $this->result->property_type . ' , ' . $this->result->location_title ?></small>
</h3>
<p>
  <?php foreach ($pathway as $path) : ?>
    &raquo;
    <a href="<?php echo JRoute::_('index.php?option=com_fcsearch&Itemid=165&s_kwds=' . JApplication::stringURLSafe($path)) ?>"><?php echo JString::ucwords(str_replace('-', ' ', $path)) ?>
    </a>

  <?php endforeach; ?>

<div class="row-fluid">
  <div class="span4"><a href="<?php echo $route ?>" class="thumbnail pull-left">
      <img src='/images/property/<?php echo $this->result->unit_id . '/thumb/' . $this->result->thumbnail ?>' class="img-rounded" />
    </a>
  </div>
  <div class="span6">
    <p><strong>
        <?php echo $this->escape(strip_tags($this->result->tagline)); ?>
      </strong>
    </p>
    <p class="small">
      <?php echo $this->escape(strip_tags($this->result->description)); ?>&nbsp;&hellip;
    </p>

    <hr class="condensed" />
    <span class="small">
      <?php echo JText::sprintf('COM_ACCOMMODATION_SITE_OCCUPANCY_DETAIL', $this->result->bedrooms, $this->result->accommodation_type, $this->result->property_type, $this->result->occupancy); ?>
    </span>
  </div>
  <div class="span2" style="text-align:right;">
    <p class="">
      <?php
      if ($this->result->price) {
        echo JText::_('COM_FCSEARCH_SEARCH_FROM');
        ?>
        <span class="lead">

          <?php
          if ($this->result->base_currency != '£') { // Must be  EURO
            $this->result->price = $this->currencies['GBP']->exchange_rate * $this->result->price;
          }

          echo '&pound;' . round($this->result->price);
          ?>
        </span>
        <br />
        <span class="small"><?php echo $this->result->tariff_based_on; ?><span>

            <?php
          } else {
            echo JText::_('COM_ACCOMMODATION_RATES_AVAILABLE_ON_REQUEST');
          }
          ?>
          </p>

          <?php if ($this->result->reviews) : ?>
            <p class="small">
              <?php echo JText::sprintf('COM_ACCOMMODATION_PROPERTY_HAS_NUMBER_OF_REVIEWS', $this->result->reviews); ?>
            </p>
          <?php endif; ?>


          <a href="<?php echo JRoute::_('index.php?option=com_accommodation&view=property&id=' . $this->result->id) ?>" class="btn  btn-primary pull-right">
            <?php echo JText::_('VIEW') ?>
          </a>
          </div>
          </div>
          </li>