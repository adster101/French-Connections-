<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('dropdown.init');

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
$data['progress'] = $this->items;
$data['form'] = $this->form;
?>
<div class="row-fluid">

  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span8">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>

      <form action="<?php echo JRoute::_('index.php?option=com_helloworld'); ?>" method="post" name="adminForm" class="form-validate" id="adminForm">
        <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_BLURB'); ?>
        <?php
          $layout = new JLayoutFile('progress', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
          echo $layout->render($data);
        ?>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>
              </th>
            </tr>
          </thead>
          <tr>
            <td width="15%">
              <strong>Property details</strong>
            </td>
            <td>
              <?php echo JHtmlProperty::progressButton($this->items[0]->id, $this->items[0]->unit_id, 'propertyversions', 'edit', 'compass', 'COM_HELLOWORLD_HELLOWORLD_PROPERTY_DETAILS', $this->items[0], 'parent_id', 'btn') ?>
            </td>
          </tr>
          <tfoot>
            <tr>
              <td colspan="7"></td>
            </tr>
          </tfoot>
        </table>
        <table class="table table-striped" id="articleList">
          <thead>
            <tr>
              <th width="2%">
                <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
              </th>
              <?php if ($canDo->get('core.edit.state')) : ?>
                <th>
                  <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_ACTIVE'); ?>
                </th>
              <?php endif; ?>
              <th>
                Ordering
              </th>
              <th colspan="2">
                Unit name
              </th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($this->items as $i => $item): ?>
              <?php if ($canEditOwn) : ?>
                <tr>
                  <td>
                    <?php echo JHtml::_('grid.id', $i, $item->unit_id); ?>
                  </td>
                  <?php if ($canDo->get('core.edit.state')) : ?>
                    <td>
                      <?php echo JHtml::_('jgrid.published', $item->published, $i, 'units.', $canPublish, 'cb', $item->created_on); ?>
                    </td>
                  <?php endif; ?>
                  <td>
                    <?php echo $this->pagination->orderUpIcon($i, true, 'units.orderup', 'JLIB_HTML_MOVE_UP', 1); ?>
                    <?php echo $this->pagination->orderDownIcon($i, count($this->items), true, 'units.orderdown', 'JLIB_HTML_MOVE_DOWN', 1); ?>
                  </td>
                  <td>
                    <strong><?php echo JText::_($item->unit_title) ?></strong>
                  </td>

                  <td>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'unitversions', 'edit', 'home', 'COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS', $item, 'unit_id', 'btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'images', 'manage', 'pictures', 'IMAGE_GALLERY', $item, 'unit_id', 'btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'availability', 'manage', 'calendar', 'COM_HELLOWORLD_SUBMENU_MANAGE_AVAILABILITY', $item, 'unit_id', 'btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'tariffs', 'manage', 'briefcase', 'COM_HELLOWORLD_SUBMENU_MANAGE_TARIFFS', $item, 'unit_id', 'btn') ?>
                    <?php echo JHtmlProperty::progressButton($item->id, $item->unit_id, 'unitversions', 'reviews', 'briefcase', 'COM_HELLOWORLD_SUBMENU_MANAGE_REVIEWS', $item, 'unit_id', 'btn') ?>
                  </td>

                </tr>
              <?php else : ?>
              <?php endif; ?>
              </form>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="7"></td>
            </tr>
          </tfoot>
        </table>
        <input type="hidden" name="extension" value="<?php echo 'com_helloworld'; ?>" />
        <input type="hidden" name="boxchecked" value="" />

        <?php echo $this->pagination->getListFooter(); ?>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="parent_id" value="<?php echo $this->id ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>

    <div class="span2">

    </div>
  </div>
