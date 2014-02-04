<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


?>

<!-- Modal -->
<div id="myModal" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel"><?php echo JText::_('COM_SHORTLIST_PLEASE_LOGIN') ?></h3>
  </div>
  <div class="modal-body">
    <div class="loading">Please wait...</div>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
  </div>
</div>