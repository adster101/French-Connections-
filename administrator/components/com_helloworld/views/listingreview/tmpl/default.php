<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php /* <ul class="nav nav-tabs">
  <li>

  <a href="<?php echo JRoute::_('index.php?option=com_helloworld&parent_id=' . $this->item->parent_id) ?>">
  <?php //$review = ($this->item->review == 1) ? 'warning' : 'publish'; ?>
  <i class="icon <?php echo 'icon-' . $review ?>"></i>
  Property Detail
  </a>
  </li>
  <?php foreach ($this->units as $unit) { ?>
  <li>
  <?php $review = ($unit->review_unit == 1) ? 'warning' : 'publish'; ?>

  <a href="<?php echo JRoute::_('index.php?option=com_helloworld&view=listingreview&parent_id=' . (int) $unit->parent_id . '&unit_id=' . (int) $unit->unit_id) ?>">
  <?php if ($review) : ?>
  <i class="icon <?php echo 'icon-' . $review ?>"></i>
  <?php endif; ?>
  <?php echo $unit->unit_title; ?><br />
  </a>
  </li>
  <?php } ?>
  </ul> */ ?>
<div class="row-fluid">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="" class="span10">
    <?php else : ?>
      <div class="span12">
      <?php endif; ?>
      <table class="table table-bordered table-hover table-striped">
        <thead>
        <th>Field</th>
        <th>Old</th>
        <th>New</th>
        </thead>
        <tbody>
          <?php foreach ($this->versions[0] as $key => $value) : ?>
            <tr>
              <td width="20%">
                <?php echo $key; ?>
                <?php if (count($this->versions[1])) : ?>
                  <?php if (strcmp(trim($this->versions[1][$key]), trim($this->versions[0][$key])) != 0) : ?>
                    <span class="label label-important">*</span>
                  <?php endif; ?>
                <?php endif; ?>
              </td>
              <td width="40%">
                <?php echo strip_tags($this->versions[0][$key]) ?>
              </td>
              <td width="40%">
                <?php if (array_key_exists($key, $this->versions[1])) : ?>
                  <?php echo $this->versions[1][$key] ?>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>