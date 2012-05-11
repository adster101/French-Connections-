<?php
/**
* @version 1.4.0
* @package RSform!Pro 1.4.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
$tmpl = JRequest::getVar('tmpl');
?>
<?php if ($tmpl != 'component') { ?>
<div class="button2-left">
	<div class="blank">
		<a rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="index.php?option=com_rsform&amp;task=forms.emails&amp;tmpl=component&amp;formId=<?php echo $this->formId; ?>" class="modal"><?php echo JText::_('RSFP_FORM_EMAILS_NEW'); ?></a>
	</div>
</div>
<br /><br />

<div id="emailscontent">
<?php } ?>
<table class="adminlist">
	<thead>
		<tr>
			<th><?php echo JText::_('RSFP_FORM_EMAILS_SUBJECT'); ?></th>
			<th width="55%" align="center"><?php echo JText::_('RSFP_FORM_EMAILS_TO'); ?></th>
			<th width="7%" class="title"><?php echo JText::_('RSFP_FORM_EMAILS_ACTIONS'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $k = 0; ?>
		<?php if (!empty($this->emails)) { ?>
		<?php foreach ($this->emails as $row) { ?>
		<tr class="row<?php echo $k; ?>">
			<td>
				<a rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="index.php?option=com_rsform&amp;task=forms.emails&amp;tmpl=component&amp;formId=<?php echo $row->formId; ?>&amp;cid=<?php echo $row->id; ?>" class="modal"><?php echo $this->escape($row->subject); ?></a>
			</td>
			<td><?php echo $this->escape($row->to); ?></td>
			<td align="center">
				<a rel="{handler: 'iframe', size: {x: 800, y: 600}}" href="index.php?option=com_rsform&amp;task=forms.emails&amp;tmpl=component&amp;formId=<?php echo $row->formId; ?>&amp;cid=<?php echo $row->id; ?>" class="modal">
					<?php echo JText::_('EDIT'); ?>
				</a> 
				/ 
				<a href="javascript: void(0)" onclick="javascript: removeEmail(<?php echo $row->id; ?>,<?php echo $row->formId; ?>);">
					<?php echo JText::_('DELETE'); ?>
				</a>
			</td>
		</tr>
		<?php $k=1-$k; ?>
		<?php } ?>
		<?php } ?>
	</tbody>
</table>
<?php if ($tmpl != 'component') { ?>
</div>
<?php } ?>