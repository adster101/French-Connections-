<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$user		= JFactory::getUser();
$userId		= $user->get('id');

?>
	<fieldset id="filter-bar">
		<div class="filter-search btn-group pull-left">
			<label class="element-invisible" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" 
             title="<?php echo JText::_('COM_CATEGORIES_ITEMS_SEARCH_FILTER'); ?>" 
             placeholder="<?php echo JText::_('COM_CATEGORIES_ITEMS_SEARCH_FILTER'); ?>" />
      <div class="btn-group pull-left hidden-phone">
        <button class="btn tip hasTooltip" type="submit" data-originall-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
        <button class="btn tip hasTooltip" type="button" onclick="document.id('filter_search').value='';this.form.submit();" data-original-title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
      </div>
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
  
    <th width="3%">
      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_PUBLISHED'); ?>
    </th>	
	<th width="">
		<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_MODIFIED'); ?>
	</th>
	<th width="">
		<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_DATE_EXPIRY'); ?>
	</th>
  <th width="3%">
		<?php echo JText::_('JGRID_HEADING_ORDERING'); ?>
	</th>
	<th width="12%">
		<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_CREATED_BY'); ?>
	</th>
</tr>
