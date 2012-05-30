<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');


$this->ordering 	= '1,2,3';

$user		= JFactory::getUser();
$userId		= $user->get('id');
$groups = $user->getAuthorisedGroups();
?>
<?php foreach($this->items as $i => $item): 
	$orderkey	= array_search($item->id, $this->ordering[$item->parent_id]);
	$canEditOwn	= $user->authorise('core.edit.own',	'com_helloworld') && $item->created_by == $userId || in_array(8, $groups);
	$canChange = true;
	$saveOrder = true;
?>

<?php if ($canEditOwn) : ?>
	<tr class="row<?php echo $i % 2; ?>">
		<td>		
			<?php	echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>

		<td>
			<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level-1) ?>

			<a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=location.edit&id='.(int) $item->id); ?>">
				<?php echo $this->escape($item->greeting); ?>
			</a>
		</td>
					<td class="order">
						<?php

							if ($canChange) : ?>
							<?php if ($saveOrder) : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$item->parent_id][$orderkey - 1]), 'categories.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$item->parent_id][$orderkey + 1]), 'categories.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
							<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
							<input type="text" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="text-area-order" />
							<?php $originalOrders[] = $orderkey + 1; ?>
						<?php else : ?>
							<?php echo $orderkey + 1;?>
						<?php endif; ?>
					</td>		
		<td>
			<?php echo JText::_($item->modified); ?>
		</td>		
	</tr>					
	<?php else : ?>
	<?php endif; ?>
<?php endforeach; ?>
