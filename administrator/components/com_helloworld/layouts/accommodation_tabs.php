<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$data = $displayData;

$input = JFactory::getApplication()->input;
$view = $input->get('view', '', 'string');
print_r($data);die;
// Fudge the ID in case we are editing the listing details
$id = ($view != 'property') ? $data['item']->parent_id : $data['item']->id;

?>
<?php if (count($data['units']) > 1) : ?>
  <div>
    <p>You have the following units:</p>
    <div class="btn-group">
      <a class="btn dropdown-toggle btn-small" data-toggle="dropdown" href="#">
        - Please choose unit to edit -
       
        <span class="caret"></span>
      </a>
      <ul class="dropdown-menu">
        <?php foreach ($data['units'] as $value) : ?>
        <li>
          <a href="<?php echo JText::_('index.php?option=com_helloworld&task=unit.edit&id='.$value->id)?>">
            <?php echo $value->unit_title;?>
          </a>
        </li>
        <?php endforeach; ?>
        
      </ul>
    </div>
  </div>
<?php endif; ?>
<hr />
<ul class="nav nav-tabs">
  <li <?php echo ($view == 'property') ? 'class=\'active\'' : '' ?>>
    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=property.edit&id=' . $id) ?>">
      <?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_LISTING_DETAILS') ?>

      <?php if ($data['item']->id && $data['item']->latitude && $data['item']->latitude && $data['item']->department && $data['item']->title) : ?>
        <i class="icon icon-ok"></i>
      <?php else: ?>
        <i class="icon icon-warning"></i>
      <?php endif; ?>
    </a>
  </li>
  <li <?php echo ($view == 'unit') ? 'class=\'active\'' : '' ?>>
    <a href="<?php echo JRoute::_('index.php?option=com_helloworld&task=unit.edit&id=' . $unit_id) ?>">
      <?php echo $data['units'][0]->unit_title ?>
      <?php echo ($data['units'][0]->unit_title) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
    </a>
  </li>
  <li>
    <a href="#">Image gallery
      <?php echo ($data['progress']['images']) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
    </a>
  </li>
  <li>
    <a href="#">Availability
      <?php echo ($data['progress']['availability']) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>
    </a>
  </li>
  <li>
    <a href="#">Facilities
      <?php echo ($data['progress']['facilities']) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>

    </a>
  </li>
  <li>
    <a href="#">Tariffs
      <?php echo ($data['progress']['tariffs']) ? '<i class=\'icon icon-ok\'></i>' : '<i class=\'icon icon-warning\'></i>'; ?>

    </a>
  </li>

</ul>
