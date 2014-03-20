<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

$lang = JFactory::getLanguage();
$lang->load('com_accommodation', JPATH_SITE, null, false, true);

$doc = JFactory::getDocument();
$app = JFactory::getApplication();

$uri = JUri::current();
// System profiler

if ($this->results === false) { 
  echo $this->loadTemplate('no_results');
} else {
  echo $this->loadTemplate('results');
}
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