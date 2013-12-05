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
?>


      <?php foreach ($this->items as $key => $item) : ?>
        <?php $prices = JHtml::_('general.price', $item->price, $item->base_currency, '', ''); ?>
        <?php if ($item->title) : ?>     
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
                  &pound; <?php echo $prices['GBP'] ?>
                <?php endif; ?>
              </p>
            </div>      
          </div>
          <?php if ($key + 1 != count($this->items)) : ?>
            <hr />
          <?php endif; ?>
        <?php endif; ?>
      <?php endforeach; ?>
 


