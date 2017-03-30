<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_status
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$hideLinks = $input->getBool('hidemainmenu');
$task = $input->getCmd('task');
$output = array();
?>
<ul class="list-striped">
  <?php if (count($msgs) > 0) : ?>
    <?php foreach ($msgs as $k => $v) : ?>
      <li>
        <?php echo JFactory::getDate($v->date_time)->calendar('d M Y'); ?> &ndash; 
        <a href="<?php echo JRoute::_('index.php?option=com_fcmessages&view=fcmessage&message_id=' . (int) $v->message_id); ?>" class="message">
          <?php if ($v->state) : ?>
            <?php echo htmlspecialchars($v->subject, ENT_COMPAT, 'UTF-8'); ?>
          <?php else: ?>
            <strong><?php echo htmlspecialchars($v->subject, ENT_COMPAT, 'UTF-8'); ?></strong>        

          <?php endif; ?>
        </a>
      </li>
    <?php endforeach; ?>

  <?php else: ?>
    <li>
      <span>No unread messages</span>
    </li>
  <?php endif; ?> 
</ul>

<p class="align-right">
  <a class="" href="<?php echo JRoute::_('index.php?option=com_fcmessages'); ?>">
    <strong>
      <?php echo JText::_('COM_OWNER_MESSAGES_VIEW_ALL'); ?>
    </strong>
  </a>
</p>
