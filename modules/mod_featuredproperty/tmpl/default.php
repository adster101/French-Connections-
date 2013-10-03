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

<div class="row-fluid">
  <div class="span12">
    <div class="well well-small">
      <?php
      foreach ($this->items as $item) {
        if ($item->title) {
          ?>     
          <div class="row-fluid">
            <div class="span6"> 
              <p>
                <a class="" href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $item->id . '&unit_id=' . (int) $item->unit_id) ?>">
                  <img src='/images/property/<?php echo $item->unit_id . '/thumb/' . $item->thumbnail ?>' class="img-rounded" />
                </a>
              </p>
            </div>
            <div class="span6">
              <p>
                <strong><?php echo $item->title; ?></strong> | 
                <?php echo JText::_('MOD_FEATURED_PROPERTY_SLEEPS'); ?>
                <?php echo $item->occupancy; ?>
                <?php if (!empty($item->price)) : ?> |
                  <?php echo '&pound;' . $item->price; ?>
                <?php endif; ?>
              </p>
            </div>      
          </div>
        <?php } ?>
      <?php } ?>
    </div>
  </div>
</div>


