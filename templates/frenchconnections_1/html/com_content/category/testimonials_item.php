<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
?>
<?php
// Create a shortcut for params.
$params = $this->item->params;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$canEdit = $this->item->params->get('access-edit');
JHtml::_('behavior.framework');
?>
<?php if ($this->item->state == 0) : ?>
  <span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
<?php endif; ?>
<?php if (!empty($this->item->introtext)) : ?>
    <blockquote class>
      <p class="lead"><?php echo $this->escape(strip_tags($this->item->introtext)); ?></p>
      <small><?php echo $this->escape($this->item->title); ?> - <?php echo JFactory::getDate($this->item->created)->calendar('d M Y') ?></small>
     

    </blockquote>
    <?php if ($this->item->state == 0) : ?>
      <span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
    <?php endif; ?>
      <hr />
<?php endif; ?>

<?php echo $this->item->event->afterDisplayContent; ?>
