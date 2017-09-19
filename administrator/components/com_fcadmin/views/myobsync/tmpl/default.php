<?php
/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// no direct access
defined('_JEXEC') or die;
jimport('frenchconnections.toolbar.Fctoolbar');
JToolbar::getInstance();
?>
<form action="<?php echo JRoute::_('index.php?option=com_fcadmin'); ?>" method="post" name="invoiceForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container" class="span12">
      <?php endif; ?> 
      <?php echo JText::_('COM_FCADMIN_MYOB_SYNC_BLURB'); ?>
      <hr />
      <?php echo JToolbar::getInstance('myob')->render(); ?>
    </div>
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="" />
</form>