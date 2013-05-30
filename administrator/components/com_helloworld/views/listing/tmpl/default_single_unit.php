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

$canDo = HelloWorldHelper::getActions();
$canEditOwn = $canDo->get('core.edit.own');
$canPublish = $canDo->get('helloworld.edit.publish');
$canSubmitForReview = $canDo->get('helloworld.property.submit');
$canReview = $canDo->get('helloworld.property.review');
$data = array();
$data['snapshot'] = $this->items;
$data['form'] = $this->form;
$data['progress'] = $this->progress;


?>

<div class="row-fluid">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
      <?php //echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_DETAILS_HELP'); ?>
    </div>
    <div id="" class="span8">
    <?php else : ?>
      <div class="span10 form-inline">
      <?php endif; ?>

      <?php

      $layout = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
      echo $layout->render($data); ?>
      <?php

      echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_BLURB'); ?>

      <form action="<?php echo JRoute::_('index.php?option=com_helloworld'); ?>" method="post" name="adminForm" class="form-validate" id="adminForm">

        <hr />
        <table class="table table-striped" id="articleList">
          <thead>
            <tr></tr>
          </thead>
          <tbody>
            <?php foreach ($this->items as $i => $item): ?>
              <?php if ($canEditOwn) : ?>
                <?php $urlParam = (empty($item->unit_id) ? 'listing_id' : 'unit_id'); ?>
                <tr>
                  <td>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'propertyversions', 'edit', 'compass', 'COM_HELLOWORLD_HELLOWORLD_PROPERTY_DETAILS', $item,'parent_id','btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'unitversions', 'edit', 'home', 'COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS', $item, 'unit_id','btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'images', 'manage', 'pictures', 'IMAGE_GALLERY', $item,'unit_id','btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'availability', 'manage', 'calendar', 'COM_HELLOWORLD_SUBMENU_MANAGE_AVAILABILITY', $item,'unit_id','btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'tariffs', 'manage', 'briefcase', 'COM_HELLOWORLD_SUBMENU_MANAGE_TARIFFS', $item,'unit_id','btn') ?>
                  </td>
                </tr>
              <?php else : ?>
              <?php endif; ?>
            <?php endforeach; ?>
            </form>
          <input type="hidden" name="extension" value="<?php echo 'com_helloworld'; ?>" />
          </tbody>
          <tfoot>
            <tr>
              <td colspan="7"></td>
            </tr>
          </tfoot>
        </table>
        <?php echo $this->pagination->getListFooter(); ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="parent_id" value="<?php echo $item->id ?>" />

        <?php echo JHtml::_('form.token'); ?>
    </div>
    <div class="span2">
      <h4>Key</h4>
      <p>
        <i class="icon icon-warning"></i>
        Please complete
      </p>
      <p>
        <i class="icon icon-publish"></i>
        Section complete
      </p>
    </div>
  </div>


