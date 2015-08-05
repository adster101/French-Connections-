<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('dropdown.init');

$arr = JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');


$listDirn = $this->escape($this->state->get('list.direction'));
$listOrder = $this->escape($this->state->get('list.ordering'));

$data = array();
$data['progress'] = $this->items;
$data['form'] = $this->form;
$data['status'] = $this->status;
$canDo = RentalHelper::getActions();
?>

<div class="row-fluid">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="" class="span12">
    <?php else : ?>
      <div class="span12 form-inline">
      <?php endif; ?>

      <form action="<?php echo JRoute::_('index.php?option=com_rental&view=listing&id=' . (int) $this->id); ?>" method="post" name="adminForm" class="form-validate form-horizontal" id="adminForm">

        <?php
        $layout = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
        echo $layout->render($data);
        ?>

        <?php
        if (count($this->items) == 1)
        {
          echo $this->loadTemplate('single_unit');
        }
        else if (count($this->items) > 1)
        {
          echo $this->loadTemplate('multi_unit');
        }
        else
        {
          echo $this->loadTemplate('no_units');
        }
        ?>

        <input type="hidden" name="extension" value="<?php echo 'com_rental'; ?>" />

        <hr />
        <h4><?php echo JText::_('COM_RENTAL_ADD_NEW_UNIT_TITLE'); ?></h4>
        <p>
          <?php echo JText::_('COM_RENTAL_ADD_NEW_UNIT_BLURB'); ?>
        </p>
        <p>
          <a id="newUnit" class="btn btn-success" href="<?php echo JRoute::_('index.php?option=com_rental&task=unitversions.add&property_id=' . (int) $this->items[0]->id); ?>">
            <i class="icon icon-plus"></i>&nbsp;
            <?php echo JText::_('COM_RENTAL_HELLOWORLD_ADD_NEW_UNIT'); ?>
          </a>       
        </p>

        <?php echo $this->pagination->getListFooter(); ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="property_id" value="<?php echo $this->id ?>" />
        <?php echo JHtml::_('form.token'); ?> .
      </form>
    </div>
  </div>

