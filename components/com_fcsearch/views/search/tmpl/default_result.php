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

$pathway = explode('/',$this->result->path);
$route = JRoute::_('index.php?option=com_accommodation&view=property&id='.$this->result->id);
?>
<li>
  <h3 class="result-title <?php echo $mime; ?>">
    <a href="<?php echo JRoute::_($route); ?>"><?php echo $this->result->property_title; ?></a>
    <small><?php echo $this->result->location_title ?></small>
  </h3>
  <p>
  <?php foreach ($pathway as $path) : ?>

  <a href="<?php echo JRoute::_($route); ?>">
    <?php echo $path ?>
  </a>>
  <?php endforeach; ?>
  </p>
  <a href="<?php echo JRoute::_('index.php?option=com_accommodation&view=property&id='.$this->result->id) ?>" class="thumbnail pull-left">
    <?php if ($this->result->parent_id = 1) : ?>
      <img src='/images/<?php echo $this->result->id . '/thumb/' . str_replace('.', '_175x100.', $this->result->thumbnail)?>' class="img-rounded" />
    <?php else: ?>
      <img src='/images/<?php echo $this->result->parent_id . '/thumb/' . str_replace('.', '_175x100.', $this->result->thumbnail) ?>' class="img-rounded" />
    <?php endif; ?>
  </a>
  <p>
		<?php echo JHtml::_('string.truncate', strip_tags($this->result->description)); ?>
  </p>
	<small class="small result-url"><?php echo $base . JRoute::_(''); ?></small>
</li>