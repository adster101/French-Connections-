<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$listDirn	= $this->escape($this->state->get('list.direction'));
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listOrder	= $this->escape($this->state->get('list.ordering'));

$ordering 	= ($listOrder == 'lft');
$user		= JFactory::getUser();
$userId		= $user->get('id');
$canChange	= $user->authorise('core.edit.state',	'com_classification');

?>

<tr>
	<th width="2%">
		<?php echo JText::_('COM_CLASSIFICATION_CLASSIFICATION_HEADING_ID'); ?>
	</th>
	<th width="2%">
		<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>			
	<th>
		<?php echo JText::_('COM_CLASSIFICATION_CLASSIFICATION_TITLE'); ?>
	</th>
	<th width="3%">
		<?php echo JText::_('COM_CLASSIFICATION_CLASSIFICATION_PUBLISHED'); ?>
	</th>	

	<th width="10%">
    <?php if ($canChange) :?>
				<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ORDERING', 'a.lft', $listDirn, $listOrder); ?>
					<?php if ($ordering) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'classifications.saveorder'); ?>
					<?php endif; ?>	
    <?php else : ?>
      		<?php echo JText::_('JGRID_HEADING_ORDERING'); ?>

    <?php endif; ?>
  </th>
</tr>
