<?php
/**
 * @package     Joomla.Tutorials
 * @subpackage  Module
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

// Register the Special Offers helper file
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');
$Itemid_property = SearchHelper::getItemid(array('component', 'com_accommodation'));
?>

<?php foreach ($items as $key => $item) : ?>
  <?php
  $prices = JHtml::_('general.price', $item->price, $item->base_currency, '', '');
  $description = JHTml::_('string.truncate', $item->description, 75, true, false);
  $title = JText::sprintf('MOD_FEATURED_PROPERTY_THUMB_TITLE', $item->id, $description);
  ?>
  <?php if ($item->title) : ?>     
    <div class="clearfix">
      <?php if (!empty($item->unit_title)) : ?>
        <h4><?php echo htmlspecialchars($item->unit_title) ?></h4>
      <?php endif ?>
      <?php if (!empty($item->offer)) : ?>
        <p class="offer">
          <strong><?php echo JText::_('COM_FCSEARCH_SPECIALOFFER'); ?></strong>
          <?php echo htmlspecialchars($item->offer); ?>
        </p>
      <?php endif; ?>
      <p>
        <a title="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" class="" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid_property . '&id=' . (int) $item->id . '&unit_id=' . (int) $item->unit_id) ?>">
          <img src='/images/property/<?php echo $item->unit_id . '/thumb/' . $item->thumbnail ?>' class="thumbnail img-rounded pull-left" />
        </a>
      </p>
      <p>
        <strong><?php echo htmlspecialchars($item->title); ?></strong> | 
        <?php echo JText::_('MOD_FEATURED_PROPERTY_SLEEPS'); ?>
        <?php echo $item->occupancy; ?>
        <?php if (!empty($item->price)) : ?> |&nbsp;&pound;<?php echo $prices['GBP'] ?>
        <?php endif; ?>
      </p>
    <?php endif; ?>
  </div> 
  <hr />

<?php endforeach; ?>



