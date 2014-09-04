<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('dropdown.init');

$arr = JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');


$listDirn = $this->escape($this->state->get('list.direction'));
$listOrder = $this->escape($this->state->get('list.ordering'));

$user = JFactory::getUser();
$userId = $user->get('id');
$groups = $user->getAuthorisedGroups();
$ordering = ($listOrder == 'a.lft');
$originalOrders = array();

$canDo = RentalHelper::getActions();
$canEditOwn = $canDo->get('core.edit.own');

$data = array();
$data['progress'] = $this->items;
$data['form'] = $this->form;
$data['status'] = $this->status;
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

      <form action="<?php echo JRoute::_('index.php?option=com_rental'); ?>" method="post" name="adminForm" class="form-validate form-horizontal" id="adminForm">

        <?php
        $layout = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
        echo $layout->render($data);
        ?>

        <table class = "table table-striped" id = "articleList">
          <thead>
            <tr></tr>
          </thead>
          <tbody>
            <?php foreach ($this->items as $i => $item):
              ?>
              <?php if ($canEditOwn) : ?>
                <?php $urlParam = (empty($item->unit_id) ? 'listing_id' : 'unit_id'); ?>
                <tr>
                  <td>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'propertyversions', 'edit', 'compass', 'COM_RENTAL_HELLOWORLD_PROPERTY_DETAILS', $item, 'property_id', 'btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'unitversions', 'edit', 'home', 'COM_RENTAL_HELLOWORLD_ACCOMMODATION_DETAILS', $item, 'unit_id', 'btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'images', 'manage', 'pictures', 'IMAGE_GALLERY', $item, 'unit_id', 'btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'tariffs', 'edit', 'briefcase', 'COM_RENTAL_SUBMENU_MANAGE_TARIFFS', $item, 'unit_id', 'btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'availability', 'manage', 'calendar', 'COM_RENTAL_SUBMENU_MANAGE_AVAILABILITY', $item, 'unit_id', 'btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'contactdetails', 'edit', 'envelope', 'COM_RENTAL_SUBMENU_MANAGE_CONTACT_DETAILS', $item, 'property_id', 'btn') ?>

                  </td>
                <tr>
                  <td colspan="7">
                    <a id="newUnit" class="btn btn-success" href="<?php echo JRoute::_('index.php?option=com_rental&task=unitversions.add&property_id=' . (int) $this->items[0]->id); ?>">
                      <i class="icon icon-plus"></i>&nbsp;
                      <?php echo JText::_('COM_RENTAL_HELLOWORLD_ADD_NEW_UNIT'); ?>
                    </a>
                  </td>
                </tr>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          <input type="hidden" name="extension" value="<?php echo 'com_rental'; ?>" />
          </tbody>
          <tfoot>
            <tr>
              <td colspan="7"></td>
            </tr>
          </tfoot>
        </table>
        <?php echo $this->pagination->getListFooter(); ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="property_id" value="<?php echo $item->id ?>" />

        <?php echo JHtml::_('form.token'); ?> .
      </form>
    </div>
  </div>


