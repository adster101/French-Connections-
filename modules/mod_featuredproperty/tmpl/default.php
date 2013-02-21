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
          <a href="index.php?option=com_accommodation&view=property&id=<?php echo $item->id ?>&lang=<?php echo $this->lang ?>">
            <large><?php echo $item->title ?></large>
          </a>
          <a class="thumbnail" href="index.php?option=com_accommodation&view=property&id=<?php echo $item->id ?>&lang=<?php echo $this->lang ?>">
            <?php if ($item->parent_id == 1) : ?>
              <img src='/images/property/<?php echo $item->id . '/thumb/' . str_replace('.', '_210x120.', $item->thumbnail) ?>' class="img-rounded" />
            <?php else: ?>
              <img src='/images/property/<?php echo $item->parent_id . '/thumb/' . str_replace('.', '_210x120.', $item->thumbnail) ?>' class="img-rounded" />
            <?php endif; ?>
          </a>
          <p><?php echo JText::_('MOD_FEATURED_PROPERTY_SLEEPS'); echo $item->occupancy;?></p>
        </div>
      <?php } ?>
    <?php } ?>	
  </div>
</div>


