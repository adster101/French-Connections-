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
<p><?php echo JText::_('MOD_LISTINGS_BLURB'); ?></p>

<div class="page-header clearfix">
  <a class="btn btn-large btn-success pull-right" href="index.php?option=com_rental&amp;task=propertyversions.add"> 
    <span class="icon icon-plus-2"></span>&nbsp;&nbsp;<?php echo JText::_('COM_RENTAL_HELLOWORLD_ADD_NEW_PROPERTY'); ?>
  </a>
  <h3><?php echo JText::_('COM_RENTAL_SUBMENU_RENTAL_ACCOMMODATION'); ?></h3>

</div>

<?php foreach ($items as $k => $item) : ?> 
  <?php
  $days_to_renewal = RentalHelper::getDaysToExpiry($item->expiry_date);
  $auto_renew = (!empty($item->VendorTxCode)) ? true : false;
  ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <span>Property reference: <?php echo (int) $item->id ?></span>
    </div>


    <div class="panel-body">
      <p class="pull-right">
        Additional marketing<br />
        Stats
      </p>
      <?php echo JHtml::_('general.image', '/images/property/' . $item->unit_id . '/thumb/' . $item->thumbnail, 'thumbnail pull-left') ?>
      <?php if ($days_to_renewal <= 28 && $days_to_renewal >= 0 && !empty($days_to_renewal)) : // Property is expiring in the next 28 days ?>
        <span class="label label-warning">
          <?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_DAYS_TO_RENEWAL', $days_to_renewal); ?>
          <?php echo JHtml::_('property.renewalButton', $days_to_renewal, $item->id, $item->review, 0, $item->expiry_date); ?>
        </span>


      <?php elseif ($days_to_renewal < 0 && !empty($days_to_renewal)) : // Property must have expired  ?>
        <span class="alert alert-danger">
          <?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_PROPERTY_EXPIRED'); ?>
          <?php echo JHtml::_('property.renewalButton', $days_to_renewal, $item->id, $item->review, 0, $item->expiry_date); ?>
        </span>
           <?php endif; ?>  
 
        <div class="btn-group">
          <a class="btn" href="<?php echo JRoute::_('index.php?option=com_rental&task=listing.view&id=' . (int) $item->id) . '&' . JSession::getFormToken() . '=1'; ?>">
            Review and renew property <?php echo (int) $item->id ?>
          </a>    
          <button class="btn dropdown-toggle" data-toggle="dropdown">
            <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <li>
              <a href="<?php echo JRoute::_('index.php?option=com_rental&task=propertyversions.edit&property_id=' . (int) $item->id) . '&' . JSession::getFormToken() . '=1'; ?>">
                <i class="icon icon-location">&nbsp;</i>Location
              </a>

            </li>   
            <li>
              <a href="<?php echo JRoute::_('index.php?option=com_rental&task=unitversions.edit&unit_id=' . (int) $item->unit_id) . '&' . JSession::getFormToken() . '=1'; ?>">
                Description
              </a>
            </li>
          </ul>
        </div>


      <hr />
      <p><?php echo JHtml::_('property.autorenewalstate', $auto_renew, $item->id); ?></p>

      <p class="small">Last updated: <strong>9th April 2014</strong></p>

    </div>
  </div>
  <?php if (!(count($items) - 1) == $k) : ?>
    <hr />
  <?php endif; ?>
<?php endforeach; ?>
<hr />
<div class="page-header clearfix">
  <a href="#" class="btn btn-large btn-success pull-right">
    <span class="icon icon-plus-2"></span>&nbsp;&nbsp;<?php echo JText::_('New for sale property'); ?>
  </a>  
  <h3>For sale property</h3>
</div>
<div class="panel panel-default">
  <div class="panel-heading">

  </div>
  <div class="panel-body">
    <p class="lead">You don't currently have any for sale properties to manage</p>
  </div>
</div>