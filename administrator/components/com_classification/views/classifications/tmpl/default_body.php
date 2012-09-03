<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');



$listOrder	= $this->escape($this->state->get('list.ordering'));
$user		= JFactory::getUser();
$userId		= $user->get('id');
$ordering 	= ($listOrder == 'lft');
$originalOrders = array();
$extension	= $this->escape($this->state->get('filter.extension'));


foreach($this->items as $i => $item): 
	$orderkey	= array_search($item->id, $this->ordering[$item->parent_id]);
	$canCheckin	= $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;

  $canChange	= $user->authorise('core.edit.state',	$extension.'.category.'.$item->id) && $canCheckin;
?>

	<tr class="row<?php echo $i % 2; ?>">
		<td>		
			<?php	echo $item->id; ?>
		</td>
		<td>
			<?php echo JHtml::_('grid.id', $i, $item->id); ?>
		</td>

		<td class="">
			<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level-1) ?>
			<a href="<?php echo JRoute::_('index.php?option=com_classification&task=classification.edit&id='.(int) $item->id); ?>">
				<?php echo $this->escape($item->title); ?>
			</a>
		</td>
		<td class="center">
			<?php echo JHtml::_('jgrid.published', $item->published, $i, 'helloworlds.', $canChange);?>
		</td>	

		<td class="order">                                
                  
						<?php 
             if ($canChange) : ?>
							<?php if ($ordering) : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$item->parent_id][$orderkey - 1]), 'categories.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$item->parent_id][$orderkey + 1]), 'categories.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>

							<?php $disabled = $ordering ?  '' : 'disabled="disabled"'; ?>
							<input type="text" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="text-area-order" />
							<?php $originalOrders[] = $orderkey + 1; ?>
						<?php else : ?>
							<?php echo $orderkey + 1;?>
						<?php endif; ?>		
		</td>		

	</tr>					
<?php endforeach; ?>
<input type="hidden" name="extension" value="<?php echo 'com_helloworld'; ?>" />
<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
