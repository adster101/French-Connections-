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

// If this is an owner then they are not permitted to publish a special offer
if (in_array(8, $groups)) 
{
	$canChange = true;
} else {
  $canChange = false;
}

foreach($this->items as $i => $item): ?>

	<tr class="row<?php echo $i % 2; ?>">
	
		<td class="center">
			<?php echo JHtml::_('grid.id', $i, $item->offer_id); ?>
		</td>

		<td class="center">
			<a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=offer.edit&id=' . $this->item->id . '&offer_id='.(int) $item->offer_id); ?>">
				<?php echo $this->escape($item->title); ?>
			</a>
		</td>
		<td class="center">
			<?php echo JText::_($item->description); ?>
		</td>	
    <td class="center">
			<?php echo JHtml::_('jgrid.published', $item->published, $i, 'offers.', $canChange);?>
		</td>
		<td class="center">
			<?php echo JText::_($item->created_date); ?>
		</td>
		<td class="center">
			<?php echo JText::_($item->start_date); ?>
			
		</td>		
		<td class="center">
			<?php echo JText::_($item->end_date); ?>
		</td>		

	</tr>					
<?php endforeach; ?>
<input type="hidden" name="extension" value="<?php echo 'com_helloworld'; ?>" />
<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
