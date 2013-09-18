<?php
/**
 * @package     Joomla.Tutorials
 * @subpackage  Module
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;
?>

<div class="well well-small">
  <div class="row-fluid">
    <?php
    foreach ($this->items as $item) {
      if ($item->title) {
        ?>
        <div class="span3"> 
          <a class="thumbnail" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $item->id . '&unit_id=' . (int) $item->unit_id) ?>">
            <img src='/images/property/<?php echo $item->unit_id . '/thumb/' . $item->thumbnail ?>' class="img-rounded" />
          </a>
          <p>
            <strong><?php echo $item->unit_title; ?></strong> | 
            <?php echo JText::_('MOD_FEATURED_PROPERTY_SLEEPS'); ?>
            <?php echo $item->occupancy; ?>
            <?php if (!empty($item->price)) : ?> |
              <?php echo '&pound;' . $item->price; ?>
            <?php endif; ?>
          </p>
        </div>
      <?php } ?>
    <?php } ?>
  </div>
</div>


