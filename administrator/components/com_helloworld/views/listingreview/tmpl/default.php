<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<ul class="nav nav-tabs">
  <li>

    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&parent_id=' . $this->item->parent_id) ?>">
      Property Detail
    </a>
  </li>
  <?php foreach ($this->units as $unit) { ?>
    <li>
      <?php $review = ($unit->review_unit === 1) ? 'warning' : 'publish'; ?>

      <a href="<?php echo JRoute::_('index.php?option=com_helloworld&view=listingreview&parent_id=' . (int) $unit->parent_id . '&unit_id=' . (int) $unit->unit_id) ?>"> 
        <?php if ($review) : ?>
          <i class="icon <?php echo 'icon-' . $review ?>"></i>
        <?php endif; ?>
        <?php echo $unit->unit_title; ?><br />
      </a>
    </li>
  <?php } ?>
</ul>
<div class="row-fluid">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="" class="span10">
    <?php else : ?>
      <div class="span12">
      <?php endif; ?>

      <table class="table table-bordered table-hover">
        <thead>
        <th>Field</th>
        <th>Old</th>
        <th>New</th>
        </thead>
        <tbody>
          <?php foreach ($this->item->old as $key => $value) : ?>
            <?php $class = (strcmp(trim($value), trim($this->item->$key)) != 0) ? "class='error'" : ''; ?>
            <tr <?php echo $class; ?>>
              <td <?php echo $class; ?>width="20%"><?php echo $key; ?></td>
              <td width="40%"><?php echo strip_tags($value); ?></td>
              <td width="40%"><?php echo $this->item->$key; ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>


      </table>

    </div>
  </div>
