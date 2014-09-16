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

$canDo = PropertyHelper::getActions();
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

      <form action="<?php echo JRoute::_('index.php?option=com_realestate'); ?>" method="post" name="adminForm" class="form-validate form-horizontal" id="adminForm">

        <?php
        $layout = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts');
        //echo $layout->render($data);
        ?>

        <table class = "table table-striped" id = "articleList">
          <thead>
            <tr></tr>
          </thead>
          <tbody>
            <?php foreach ($this->items as $i => $item):
              ?>
              <?php if ($canEditOwn) : ?>
                <tr>
                  <td>
                    <?php echo JHtml::_('property.progressButton', 'propertyversions.edit', $this->status->property_detail, 'COM_REALESTATE_PROPERTY_DETAIL') ?>
                    <?php echo JHtml::_('property.progressButton', 'images.manage', $this->status->gallery, 'IMAGE_GALLERY') ?>
                  </td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          <input type="hidden" name="extension" value="<?php echo 'com_realestate'; ?>" />
          </tbody>
          <tfoot>
            <tr>
              <td colspan="7"></td>
            </tr>
          </tfoot>
        </table>
        <?php echo $this->pagination->getListFooter(); ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="realestate_property_id" value="<?php echo $item->id ?>" />

        <?php echo JHtml::_('form.token'); ?> .
      </form>
    </div>
  </div>


