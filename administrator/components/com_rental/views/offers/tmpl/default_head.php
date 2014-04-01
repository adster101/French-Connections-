<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
	
<tr>
	<th width="1%">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>			
	<th width="10%">
		<?php echo JText::_('COM_RENTAL_OFFERS_HEADING_GREETING'); ?>
	</th>
  <th width="10%">
		<?php echo JText::_('COM_RENTAL_OFFERS_HEADING_DESCRIPTION'); ?>
	</th>
	<th width="3%">
		<?php echo JText::_('COM_RENTAL_OFFERS_HEADING_PUBLISHED'); ?>
	</th>	
	<th width="5%">
		<?php echo JText::_('COM_RENTAL_OFFERS_HEADING_DATE_CREATED'); ?>
	</th>
	<th width="5%">
		<?php echo JText::_('COM_RENTAL_OFFERS_HEADING_DATE_START'); ?>
	</th>
  <th width="5%">
		<?php echo JText::_('COM_RENTAL_OFFERS_HEADING_DATE_END'); ?>
	</th>
</tr>