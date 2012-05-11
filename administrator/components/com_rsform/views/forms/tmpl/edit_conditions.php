<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<?php if (!$this->isComponent) { ?>
<div class="rsform_error"><?php echo JText::_('RSFP_CONDITION_MULTILANGUAGE_WARNING'); ?></div>
<br />
<div id="conditionscontent" style="overflow: auto;">
<?php } ?>
<div class="button2-left">
	<div class="blank">
		<a rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="index.php?option=com_rsform&amp;view=conditions&amp;layout=edit&amp;formId=<?php echo $this->formId; ?>&amp;tmpl=component" class="modal"><?php echo JText::_('RSFP_FORM_CONDITION_NEW'); ?></a>
	</div>
</div>

<br /><br />

	<table class="adminlist" id="conditionsTable">
	<thead>
		<tr>
			<th nowrap="nowrap"><?php echo JText::_('RSFP_CONDITION_FIELD_NAME'); ?></th>
			<th width="1%" class="title" nowrap="nowrap"><?php echo JText::_('RSFP_CONDITIONS_ACTIONS'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $i = 0; $k = 0; $n = count($this->conditions); ?>
		<?php if (!empty($this->conditions)) { ?>
		<?php foreach ($this->conditions as $row) { ?>
		<tr class="row<?php echo $k; ?>">
			<td>
			<a rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="index.php?option=com_rsform&amp;view=conditions&amp;layout=edit&amp;formId=<?php echo $this->formId; ?>&amp;cid=<?php echo $row->id; ?>&amp;tmpl=component" class="modal">
				(<?php echo JText::_('RSFP_CONDITION_'.$row->action); ?>) <?php echo $this->escape($row->ComponentName); ?>
			</a>
			</td>
			<td align="center" width="1%" nowrap="nowrap">
				<a rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="index.php?option=com_rsform&amp;view=conditions&amp;layout=edit&amp;formId=<?php echo $this->formId; ?>&amp;cid=<?php echo $row->id; ?>&amp;tmpl=component" class="modal">
					<?php echo JText::_('EDIT'); ?>
				</a>
				/ 
				<a href="javascript: void(0)" onclick="if (confirm('<?php echo JText::_('RSFP_CONDITION_DELETE_SURE', true); ?>')) conditionDelete(<?php echo $this->formId; ?>,<?php echo $row->id; ?>);">
					<?php echo JText::_('DELETE'); ?>
				</a>
			</td>
		</tr>
		<?php $k=1-$k; ?>
		<?php $i++; ?>
		<?php } ?>
		<?php } ?>
	</tbody>
	</table>
<?php if (!$this->isComponent) { ?>
</div>
<?php } ?>