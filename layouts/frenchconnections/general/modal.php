<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$title = JText::_($displayData['title']);
$id = ($displayData['id']) ? $displayData['id'] : 'collapseModal';
$task = ($displayData['task']) ? $displayData['task'] : '';

$cmd = "Joomla.submitbutton('$task')";
?>
<!-- Modal -->
<div id="<?php echo $id ?>" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo $title ?></h3>
  </div>
  <div class="modal-body">
    <!-- Content goes here -->
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>

    <button class="btn btn-primary" onclick="<?php echo $cmd; ?>">
<?php echo JText::_('JSUBMIT'); ?>
    </button>
  </div>
</div>