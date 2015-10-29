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
$params = $item->params;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$canEdit = $item->params->get('access-edit');
JHtml::_('behavior.framework');
?>
<?php if ($item->state == 0) : ?>
    <span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
<?php endif; ?>
<?php if (!empty($item->introtext)) : ?>
    <div class='testimonial'>
        <blockquote>
            <p><?php echo strip_tags($item->introtext); ?></p>
            <footer>
                <?php echo $item->title; ?>
            </footer>

        </blockquote>
    </div>
    <?php if ($item->state == 0) : ?>
        <span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
    <?php endif; ?>
<?php endif; ?>

