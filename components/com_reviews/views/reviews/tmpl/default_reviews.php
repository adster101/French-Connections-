<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;
$route = JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->result->id . '&unit_id=' . (int) $this->result->unit_id);

$user = JFactory::getUser();
$logged_in = ($user->guest) ? false : true;
?>
<li>
  <p>
    <?php echo JText::sprintf('COM_REVIEWS_SUBMITTED_REVIEW_FOR', $this->result->unit_title) ?>
  </p>
  <?php if (!empty($this->result->review_text)) : ?>
    <?php if (!empty($this->result->title)) : ?>
      <p>
        <?php echo $this->escape($this->result->title); ?>
      </p>
    <?php endif; ?>
    <figure>
      <blockquote class="quote">
        <?php echo strip_tags(JHtml::_('string.truncate', $this->result->review_text, 1250)); ?>
      </blockquote> 
      <figcaption>
        <cite>
          <?php
          $date = new DateTime($this->result->date);
          echo $date->format('D, d M Y');
          ?>
        </cite> 
      </figcaption>
    </figure>
  <?php endif; ?>
</li>
