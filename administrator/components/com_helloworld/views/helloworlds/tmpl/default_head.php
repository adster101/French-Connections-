<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_CATEGORIES_ITEMS_SEARCH_FILTER'); ?>" />
			<button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
</fieldset>
<tr>
	<th width="5">
		<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_ID'); ?>
	</th>
	<th width="20">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>			
	<th>
		<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_GREETING'); ?>
	</th>
	<th width="10%">
		<?php echo JText::_('JGRID_HEADING_ORDERING'); ?>
	</th>
	<th>
		<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_CREATED_BY'); ?>
	</th>
</tr>
