<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');


$user		= JFactory::getUser();
$userId		= $user->get('id');
$groups = $user->getAuthorisedGroups();
?>
<?php foreach($this->items as $i => $item): 

	$canEditOwn	= $user->authorise('core.edit.own',	'com_helloworld') && $item->created_by == $userId || in_array(8, $groups);

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
			<!--<a href="<?php echo JRoute::_('index.php?option=com_helloworld&view=helloworld&layout=edit&id='.(int) $item->id); ?>">-->
				<?php echo $item->greeting; ?>
			<!--</a>-->
		</td>
	</tr>					
	<?php else : ?>
	<?php endif; ?>
<?php endforeach; ?>