<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');



$listOrder = $this->escape($this->state->get('list.ordering'));
$user = JFactory::getUser();
$userId = $user->get('id');
$groups = $user->getAuthorisedGroups();
$ordering = ($listOrder == 'a.lft');
$originalOrders = array();

// If this is an owner then they are not permitted to publish a special offer
if (in_array(8, $groups)) {
  $canChange = true;
} else {
  $canChange = false;
}
foreach ($this->items as $i => $item):
  ?>

  <tr class="row<?php echo $i % 2; ?>">

    <td>
      <?php echo JHtml::_('grid.id', $i, $item->id); ?>
    </td>
    <td>
      <?php echo JText::_($item->property_id); ?>
    </td>
    <td>
      <?php echo $this->escape($item->title); ?>
  </td>
  <td>
      <?php echo JText::_($item->review_text); ?>
  </td>	
  <td>
    <?php echo JHtml::_('jgrid.published', $item->published, $i, 'reviews.', false,'cb'); ?>
  </td>
  <td>
    <?php echo JText::_($item->created); ?>
  </td>	
  <td>
    <?php echo JText::_($item->id); ?>
  </td>	
  </tr>					
<?php endforeach; ?>
<input type="hidden" name="extension" value="<?php echo 'com_rental'; ?>" />
<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
