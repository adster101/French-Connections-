<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');



$listOrder	= $this->escape($this->state->get('list.ordering'));
$user		= JFactory::getUser();
$userId		= $user->get('id');
$groups = $user->getAuthorisedGroups();
$ordering 	= ($listOrder == 'a.lft');
$originalOrders = array();

foreach($this->items as $i => $item): 
	$orderkey	= array_search($item->id, $this->ordering[$item->parent_id]);
	$canEditOwn	= $user->authorise('core.edit.own',	'com_helloworld') && $item->created_by == $userId || in_array(8, $groups) || in_array(11, $groups);
	$canChange = true;
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
			<a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=helloworld.edit&id='.(int) $item->id) . '&' . JUtility::getToken() . '=1'; ?>">
				<?php echo $this->escape($item->greeting); ?>
			</a>
		</td>
    <?php if ($user->authorise('helloworld.edit.publish', 'com_helloworld' )) : ?>

      <td class="center">
        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'helloworlds.', $canChange);?>
      </td>
    <?php endif; ?>
    <td class="center">
			<?php echo JText::_($item->modified); ?>
		</td>
		<td class="order">
			<?php
				if ($canChange) : ?>
					<span><?php if ($item->parent_id != 1) { echo $this->pagination->orderUpIcon($i, isset($this->ordering[$item->parent_id][$orderkey - 1]), 'helloworlds.orderup', 'JLIB_HTML_MOVE_UP', $ordering); } ?></span>
					<span><?php if ($item->parent_id != 1) { echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$item->parent_id][$orderkey + 1]), 'helloworlds.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); } ?></span>
				
			<?php else : ?>
				<?php echo $orderkey + 1;?>
			<?php endif; ?>
		</td>		
		<td class="center">
			<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.(int) $item->created_by); ?>">
				<?php echo JText::_($item->author_name); ?>
			</a>
		</td>	

	</tr>					
	<?php else : ?>
	<?php endif; ?>
<?php endforeach; ?>
<input type="hidden" name="extension" value="<?php echo 'com_helloworld'; ?>" />
<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
