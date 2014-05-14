<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_submenu
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$lang = JFactory::getLanguage();
$lang->load('com_rental');

//echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8');
?>
<!--<p><?php //echo JText::_('MOD_LISTINGS_BLURB');                       ?></p>-->

<div class="page-header clearfix">
  <a class="btn btn-large btn-success pull-right" href="index.php?option=com_rental&amp;task=propertyversions.add"> 
    <?php echo JText::_('COM_RENTAL_HELLOWORLD_ADD_NEW_PROPERTY'); ?>
  </a>
  <h3><?php echo JText::_('COM_RENTAL_SUBMENU_RENTAL_ACCOMMODATION'); ?></h3>
</div>
<?php if (count($items) > 0) : ?>
  <?php foreach ($items as $k => $item) : ?> 
    <?php
    $days_to_renewal = RentalHelper::getDaysToExpiry($item->expiry_date);
    $auto_renew = (!empty($item->VendorTxCode)) ? true : false;
    ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h4 style="margin:0">Property reference: <?php echo (int) $item->id ?>
          <small>
            (last updated: <strong><?php echo $item->modified ?></strong>)
          </small>
        </h4>  
      </div>
      <div class="panel-body">     
        <?php if ($days_to_renewal <= 28 && $days_to_renewal >= 7 && !empty($days_to_renewal)) : // Property is expiring in the next 28 days ?>
          <div class="alert alert-info">
            <?php echo JText::sprintf('COM_RENTAL_CONTROL_PANEL_DAYS_TO_RENEWAL', $days_to_renewal, $item->expiry_date); ?>
            <?php echo JHtml::_('property.renewalButton', $days_to_renewal, $item->id, $item->review, 0, $item->expiry_date); ?>
          </div>
        <?php elseif ($days_to_renewal <= 7 && $days_to_renewal >= 0 && !empty($days_to_renewal)) : // Property is expiring in the next 7 days ?>
          <div class="alert alert-warning">
            <?php echo JText::sprintf('COM_RENTAL_CONTROL_PANEL_DAYS_TO_RENEWAL', $days_to_renewal, $item->expiry_date); ?>
            <?php echo JHtml::_('property.renewalButton', $days_to_renewal, $item->id, $item->review, 0, $item->expiry_date); ?>
          </div>
        <?php elseif ($days_to_renewal < 0 && !empty($days_to_renewal)) : // Property must have expired  ?>
          <div class="alert alert-danger">
            <?php echo JText::sprintf('COM_RENTAL_OWNERS_CONTROL_PANEL_PROPERTY_EXPIRED', $item->expiry_date); ?>
            <?php echo JHtml::_('property.renewalButton', $days_to_renewal, $item->id, $item->review, 0, $item->expiry_date); ?>
          </div>
        <?php endif; ?>  
        <?php echo JHtml::_('general.image', '/images/property/' . $item->unit_id . '/thumb/' . $item->thumbnail, 'thumbnail pull-left') ?>
        <?php echo JHtml::_('property.editButton', $days_to_renewal, $item->id, $item->unit_id, $item->review); ?>
        <hr /> 
        <div class="pull-right">

          <p>
            <a class="latest-enquiries" rel="tooltip" title="<?php echo JText::_('COM_RENTAL_VIEW_LISTING_ENQUIRIES_TOOLTIP'); ?>" href="<?php echo JRoute::_('index.php?option=com_enquiries') ?>">
              <?php echo JText::_('COM_RENTAL_VIEW_LISTING_ENQUIRIES_TOOLTIP'); ?>
            </a>
          </p>
          <p>
            <a rel="tooltip" title="<?php echo JText::_('COM_RENTAL_VIEW_LISTING_STATISTICS_TOOLTIP'); ?>" href="<?php echo JRoute::_('index.php?option=com_stats&id=' . (int) $item->id) ?>">
              <?php echo JText::_('COM_RENTAL_VIEW_LISTING_STATISTICS'); ?>
            </a>
          </p>
          <p>
            <a href="<?php echo JRoute::_('index.php?option=com_rental&view=marketing&property_id=' . (int) $item->id) ?>">
              <i class="icon icon-wand"></i> 
              Additional marketing
            </a>  
          </p>
        </div>
        <p><?php echo JHtml::_('property.autorenewalstate', $auto_renew, $item->id); ?></p>

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