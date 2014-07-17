<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_submenu
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>
<div class="page-header clearfix">
  <a class="btn <?php echo (count($listings)) ? '' : 'btn-large' ?> btn-success pull-right" href="index.php?option=com_rental&amp;task=propertyversions.add"> 
    <?php echo JText::_('COM_RENTAL_HELLOWORLD_ADD_NEW_PROPERTY'); ?>
  </a>
  <h3><?php echo JText::_('COM_RENTAL_SUBMENU_RENTAL_ACCOMMODATION'); ?></h3>
</div>
<?php if (count($listings) > 0) : ?>
  <?php foreach ($listings as $k => $item) : ?> 
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 style="margin:0">Property reference: <?php echo (int) $item->id ?>
          <small>
            (last updated: <strong><?php echo $item->modified ?></strong>)
          </small>
        </h4>  
      </div>
      <div class="panel-body">  

        <div class="listing-container">
          <div class="listing-image-container">
            <?php echo JHtml::_('general.image', '/images/property/' . $item->unit_id . '/thumb/' . $item->thumbnail, 'thumbnail') ?>
          </div>
          <div class="listing-container-wide">
            <?php if (!empty($item->message) && $item->review != 2) : ?>
              <?php echo $item->message; ?>
            <?php endif; ?> 
            <hr />
            <?php if ($item->auto_renewal) : ?>
              <p><?php echo JText::sprintf('MOD_LISTINGS_AUTO_RENEWAL_ENABLED', $item->expiry_date); ?></p>
              <p>
                <?php echo JText::_('MOD_LISTING_AUTO_RENEWAL_STATUS_ENABLED'); ?>
                <?php echo JHtml::_('property.link', $item->id, 'COM_RENTAL_HELLOWORLD_CANCEL_AUTO_RENEWALS_CLICK_HERE', 'autorenewals.showtransactionlist', 'COM_RENTAL_HELLOWORLD_CANCEL_AUTO_RENEWALS_CLICK_HERE', '', false); ?>
              </p>
            <?php elseif (!$item->auto_renewal && !empty($item->expiry_date)) : ?>
              <p><?php echo JText::sprintf('MOD_LISTINGS_ENABLE_AUTO_RENEWAL', $item->expiry_date); ?></p>
              <p>
                <?php echo JText::_('MOD_LISTING_AUTO_RENEWAL_STATUS_NOT_ENABLED'); ?>
                <?php echo JHtml::_('property.link', $item->id, 'COM_RENTAL_HELLOWORLD_ENABLE_AUTO_RENEWALS', 'autorenewals.showtransactionlist', 'COM_RENTAL_HELLOWORLD_ENABLE_AUTO_RENEWALS', '', false); ?>
              </p>
            <?php else : ?>      
              <?php echo JText::_('MOD_LISTING_AUTO_RENEWAL_STATUS_NOT_ENABLED'); ?>

              <?php echo JHtml::_('property.link', $item->id, 'COM_RENTAL_HELLOWORLD_ENABLE_AUTO_RENEWALS', 'autorenewals.showtransactionlist', 'COM_RENTAL_HELLOWORLD_ENABLE_AUTO_RENEWALS', '', false); ?>
            <?php endif; ?>
            <?php //echo JHtml::_('property.autorenewalstate', $item->auto_renewal, $item->id); ?>
          </div>
        </div>
        <div class="listing-container-narrow-links">
          <?php echo JHtml::_('property.quicklink', 'COM_RENTAL_VIEW_LISTING_ENQUIRIES_TOOLTIP', 'index.php?option=com_enquiries', 'COM_RENTAL_VIEW_LISTING_ENQUIRIES'); ?>
          <?php echo JHtml::_('property.quicklink', 'COM_RENTAL_VIEW_LISTING_STATISTICS_TOOLTIP', 'index.php?option=com_stats&id=' . (int) $item->id, 'COM_RENTAL_VIEW_LISTING_STATISTICS'); ?>
          <?php echo JHtml::_('property.quicklink', 'COM_RENTAL_VIEW_LISTING_ADDITIONAL_MARKETING_TOOLTIP', 'index.php?option=com_rental&view=marketing', 'COM_RENTAL_VIEW_LISTING_ADDITIONAL_MARKETING'); ?>    
        </div>
      </div>
    </div>
    <?php if (!(count($items) - 1) == $k) : ?>
      <hr />
    <?php endif; ?>
  <?php endforeach; ?>
<?php else: ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      No rental property found
    </div>
    <div class="panel-body">
      <p>You don't currently have any for rental properties to manage. In case you didn't spot it click <strong>the big green button</strong> to add a rental property...</p>
    </div>
  </div>
<?php endif; ?>
<hr />
<div class="page-header clearfix">
  <a href="#" class="btn btn-large btn-success pull-right">
    <span class="icon icon-plus-2"></span>&nbsp;&nbsp;<?php echo JText::_('New for sale property'); ?>
  </a>  
  <h3>For sale property</h3>
</div>
<div class="panel panel-default">
  <div class="panel-heading">
    No for sale property found
  </div>
  <div class="panel-body">
    <p>You don't currently have any for sale properties to manage. In case you didn't spot it click <strong>the big green button</strong> to add a for sale property...</p>
  </div>
</div>