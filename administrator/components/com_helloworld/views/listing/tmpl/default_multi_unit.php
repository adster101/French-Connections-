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

<?php if (!empty($this->sidebar)): ?>
  <div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
  </div>
  <div id="j-main-container" class="span10">
  <?php else : ?>
    <div id="j-main-container">
    <?php endif; ?>



      <?php
        $layout = new JLayoutFile('submit_for_approval', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
        echo $layout->render($data);
      ?>




    <form action="<?php echo JRoute::_('index.php?option=com_helloworld'); ?>" method="post" name="adminForm" class="form-validate" id="adminForm">

      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_LISTING_BLURB'); ?>
      <table class="table table-striped">
        <thead>
        <th>
        </th>
        </thead>
        <tr>
          <td width="15%">
            <strong>Property details</strong>
          </td>
          <td>
            <?php echo JHtmlProperty::progressButton($this->items[0]->listing_id, $this->items[0]->unit_id, 'property', 'compass', 'COM_HELLOWORLD_HELLOWORLD_PROPERTY_DETAILS', $this->items[0]) ?>
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
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->items as $i => $item):

            ?>
            <?php if ($canEditOwn) : ?>
              <tr>
                <td width="15%">
                  <strong><?php echo JText::_($item->unit_title) ?></strong>
                </td>

                <td>
                  <?php echo JHtmlProperty::progressButton($item->listing_id, $item->unit_id, 'unit', 'home', 'COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS', $item) ?>
                  <?php echo JHtmlProperty::progressButton($item->listing_id, $item->unit_id, 'images', 'pictures', 'IMAGE_GALLERY', $item) ?>
                  <?php echo JHtmlProperty::progressButton($item->listing_id, $item->unit_id, 'availability', 'calendar', 'COM_HELLOWORLD_SUBMENU_MANAGE_AVAILABILITY', $item) ?>
                  <?php echo JHtmlProperty::progressButton($item->listing_id, $item->unit_id, 'tariffs', 'briefcase', 'COM_HELLOWORLD_SUBMENU_MANAGE_TARIFFS', $item) ?>
                </td>
                <td>
                  Order Up/Down
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
      <?php echo $this->pagination->getListFooter(); ?>
      <input type="hidden" name="task" value="" />
      <?php echo JHtml::_('form.token'); ?>
  </div>


