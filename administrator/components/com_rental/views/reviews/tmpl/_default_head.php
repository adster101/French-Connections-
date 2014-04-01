<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
	
<tr>
	<th>
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>			
	<th>
		<?php echo JText::_('COM_RENTAL_REVIEWS_HEADING_PRN'); ?>
	</th>
  <th>
		<?php echo JText::_('COM_RENTAL_REVIEWS_HEADING_TITLE'); ?>
	</th>
  <th>
		<?php echo JText::_('COM_RENTAL_REVIEWS_HEADING_REVIEW_TEXT'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_RENTAL_REVIEWS_HEADING_PUBLISHED'); ?>
	</th>	
	<th>
		<?php echo JText::_('COM_RENTAL_REVIEWS_HEADING_DATE_CREATED'); ?>
	</th>
  <th>
		<?php echo JText::_('COM_RENTAL_REVIEWS_HEADING_REVIEW_ID'); ?>
	</th>
</tr>