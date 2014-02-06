<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$user = JFactory::getUser();
$logged_in = ($user->guest) ? false : true;
?>
<?php if ($this->reviews) : ?>
  <figure>
    <blockquote class="quote">
      <?php echo strip_tags(JHtml::_('string.truncate', $this->reviews[0]->review_text, 100)); ?>

      <p>
        <a href="<?php echo JRoute::_('index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->item->property_id . '&unit_id=' . (int) $this->item->unit_id) ?>#reviews">
          <?php echo JText::sprintf('COM_ACCOMMODATION_SITE_READ_MORE_REVIEWS', count($this->reviews)); ?>
        </a>
      </p>
    </blockquote> 
    <figcaption>
      <cite>

        <?php echo $this->reviews[0]->guest_name; ?>
        <?php
        $date = new DateTime($this->reviews[0]->date);
        echo $date->format('D, d M Y');
        ?>
      </cite> 
    </figcaption>
  </figure>
<?php else: ?>
  <p>
    <?php echo JText::_('COM_ACCOMMODATION_SITE_NO_REVIEWS'); ?>
  </p>
<?php endif; ?>
<?php if ($logged_in) : ?>  
  <p>
    <a href="<?php echo JRoute::_('index.php?option=com_reviews&task=review.add&Itemid=194&unit_id=' . $this->item->unit_id); ?>">
      <?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_REVIEW'); ?>
    </a>
  </p>
<?php else: ?>
  <a class="login" href="#" data-return="<?php echo base64_encode(JRoute::_('index.php?option=com_reviews&view=reviews&Itemid=194&unit_id=' . $this->item->unit_id)); ?>">
    <?php echo JText::_('COM_ACCOMMODATION_SITE_ADD_REVIEW') ?>
  </a>    
<?php endif; ?>
<hr />