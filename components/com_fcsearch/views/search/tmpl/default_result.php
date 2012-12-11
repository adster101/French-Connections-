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

$pathway = explode('/', $this->result->path);
$route = JRoute::_('index.php?option=com_accommodation&view=property&id=' . $this->result->id);
?>

<li>
</p>  
<p class="pull-right">
  <a class="btn btn-small" href="#">
    <i class="icon-bookmark"> </i><?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_TO_FAVOURITES') ?>
  </a>
</p>
<h3 class="result-title <?php echo $mime; ?>">
  <a href="<?php echo JRoute::_($route); ?>"><?php echo $this->result->property_title; ?></a>
  <small><?php echo $this->result->location_title ?></small>
</h3>
<p>
  <?php foreach ($pathway as $path) : ?>

    <a href="<?php echo JRoute::_('/index.php?option=com_fcsearch&view=search&q=' . JApplication::stringURLSafe($path)) ?>">
      <?php echo $path ?>
    </a>

  <?php endforeach; ?>

<div class="row-fluid">
  <div class="span4">
    <a href="<?php echo JRoute::_('index.php?option=com_accommodation&view=property&id=' . $this->result->id) ?>" class="thumbnail pull-left">
      <?php if ($this->result->parent_id = 1) : ?>
        <img src='images/<?php echo $this->result->id . '/thumb/' . str_replace('.', '_175x100.', $this->result->thumbnail) ?>' class="img-rounded" />
      <?php else: ?>
        <img src='images/<?php echo $this->result->parent_id . '/thumb/' . str_replace('.', '_175x100.', $this->result->thumbnail) ?>' class="img-rounded" />
      <?php endif; ?>
    </a>
  </div>
  <div class="span5">

    <p>
      <?php echo JHtml::_('string.truncate', strip_tags($this->result->description)); ?>
    </p>
    <ul>
      <li>
        <?php echo JText::sprintf('COM_ACCOMMODATION_SITE_OCCUPANCY_DETAIL', $this->result->bedrooms, $this->result->accommodation_type, $this->result->property_type, $this->result->occupancy); ?>
      </li>
    </ul>
  </div>
  <div class="span3">
    <p class="">
      <?php
      if ($this->result->from_rate) {
        echo JText::_('COM_FCSEARCH_SEARCH_FROM');
        ?>
        <span class="lead">
          <?php echo $this->result->base_currency; ?><?php echo $this->result->from_rate; ?>
        </span><br />
        <span class="small"><?php echo $this->result->tariff_based_on; ?><span>

            <?php
          } else {
            echo JText::_('COM_ACCOMMODATION_RATES_AVAILABLE_ON_REQUEST');
          }
          ?>
          </p>

          <?php if ($this->result->review_count) : ?>
            <p class="small">
              <?php echo JText::sprintf('COM_ACCOMMODATION_PROPERTY_HAS_NUMBER_OF_REVIEWS', $this->result->review_count); ?>
            </p>
          <?php endif; ?>


          <a href="<?php echo JRoute::_('index.php?option=com_accommodation&view=property&id=' . $this->result->id) ?>" class="btn  btn-primary pull-right">
            <?php echo JText::_('VIEW') ?>
          </a>
          </div>
          </div>
          </li>