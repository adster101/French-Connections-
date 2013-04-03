<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));

$saveOrder = true;
if ($saveOrder) {
  $saveOrderingUrl = 'index.php?option=com_content&task=articles.saveOrderAjax&tmpl=component';
  JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$app = JFactory::getApplication();

$input = $app->input;

$id = $input->get('id', '', 'int');

$data = JApplication::getUserState('listing', '');
?>


<?php
$listOrder = $this->escape($this->state->get('list.ordering'));
$user = JFactory::getUser();
$userId = $user->id;
$groups = $user->getAuthorisedGroups();
$ordering = ($listOrder == 'a.lft');
$originalOrders = array();

foreach ($this->items as $i => $item):
  ?>
  <tr>
    <td>
      <span class="sortable-handler hasTooltip" title="<?php //echo $disabledLabel;           ?>">
        <i class="icon-move"></i>
      </span>
      <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
    </td>
    <td class="hidden-phone">
      <?php echo JHtml::_('grid.id', $i, $item->id); ?>
    </td>
    <td> 
      <img width="75" src="<?php echo '/images/property/' . (int) $this->items[0]->property_id . '/thumbs/' . $item->image_file_name; ?>" />
    </td>
    <td>  
      <input type="text" name="jform[caption" value="<?php echo $this->escape($item->caption); ?>" />
    </td>
    <td>
      <input type="radio" name="image_id[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
    </td>
    <td>
      
      <a class="btn btn-primary delete" href=<?php echo 'index.php?option=com_helloworld&task=images.delete&' . JSession::getFormToken() . '=1&id=' . (int) $item->id . '&property_id=' . (int) $this->items[0]->property_id ?>> 
         <?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_DELETE_IMAGE'); ?>
      </a>
    </td>
  </tr>				
<?php endforeach; ?>