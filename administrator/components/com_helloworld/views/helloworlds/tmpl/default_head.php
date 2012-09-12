<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$user		= JFactory::getUser();
$userId		= $user->get('id');

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
	<th width="2%">
		<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_ID'); ?>
	</th>
	<th width="2%">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>			
	<th>
		<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_GREETING'); ?>
	</th>
  <?php if ($user->authorise('helloworld.edit.publish', 'com_helloworld' )) : ?>
    <th width="3%">
      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_PUBLISHED'); ?>
    </th>	
  <?php endif; ?>
	<th width="">
		<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_CREATED'); ?>
	</th>
	<th width="3%">
		<?php echo JText::_('JGRID_HEADING_ORDERING'); ?>
	</th>
	<th width="12%">
		<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_CREATED_BY'); ?>
	</th>
</tr>
