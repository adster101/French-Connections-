<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_contact
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Below will be useful to be able to use the same params for enquiries as well as reviews
// $cparams = JComponentHelper::getParams('com_media');

$app = JFactory::getApplication();


jimport('joomla.html.html.bootstrap');
?>

<div class="page-header">
  <h1>Places of interest in France</h1>
</div> 
<h2><?php echo $this->escape($this->document->title); ?></h2>

<?php if ($this->item->description) : ?>
  <?php echo $this->item->description; ?>
<?php endif; ?>

<?php if ($this->item->public_transport) : ?>
  <h3>Getting there</h3>
  <?php echo $this->item->public_transport; ?>
<?php endif; ?>
<?php if ($this->item->location) : ?>
  <h3>Location</h3>
  <p><?php echo $this->item->location ?></p>
  
<?php endif; ?>
    <div id="map" style="width:100%;height:250px;margin-bottom:9px;" data-lat="<?php echo $this->item->latitude ?>" data-lon="<?php echo $this->item->longitude ?>"></div>

<?php if ($this->item->general_facilities) : ?>
  <h3>Facilities</h3>
  <?php echo strip_tags($this->item->general_facilities,"<p>,<br>,<span>"); ?>
<?php endif; ?>

<?php if ($this->item->website || $this->item->telephone || $this->item->email) : ?>
  <h4>Contact</h4>
  <p>
    <strong>Website: </strong>
    <a href="<?php echo $this->escape($this->item->website); ?>"><?php echo $this->escape($this->item->website); ?></a>
  </p>
  <p>
    <strong>Email: </strong>
    <?php echo $this->escape($this->item->email); ?>
  </p>
  <p>
    <strong>Tel: </strong>
    <?php echo $this->escape($this->item->telephone); ?>
  </p>
  <p>
    <strong>Address: </strong>
    <?php echo $this->escape($this->item->address); ?>
  </p>
<?php endif; ?>
  


