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

?>

<form action="<?php echo JRoute::_('index.php?option=com_helloworld'); ?>" method="post" name="adminForm" class="form-validate" id="adminForm">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>

      <table class="table table-striped" id="articleList">
        <thead>
          <tr>

            <th>
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_HEADING_GREETING'); ?>
            </th>
          </tr>
        </thead>
        <?php
        $canEditOwn = $canDo->get('core.edit.own');
        $canPublish = $canDo->get('helloworld.edit.publish');

        $canSubmitForReview = $canDo->get('helloworld.property.submit');
        $canReview = $canDo->get('helloworld.property.review');
        ?>

        <tbody>
          <?php foreach ($this->items as $i => $item): ?>

            <?php if ($canEditOwn) : ?>
              <tr class="row<?php echo $i % 2; ?>">
                <td>
                  <?php echo $item->id; ?>
                </td>
                <td>
                  <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                </td>
                <td>
                  <a class="btn" href="<?php echo JRoute::_('index.php?option=com_helloworld&task=property.edit&id=' . (int) $item->parent_id) ?>">
                    <?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_LISTING_DETAILS') ?>
                    <i class="icon icon-ok"></i> 
                  </a>
                  <a class="btn" href="<?php echo JRoute::_('index.php?option=com_helloworld&task=unit.edit&id=' . (int) $item->id) ?>">
                    <?php echo Jtext::_('COM_HELLOWORLD_HELLOWORLD_ACCOMMODATION_DETAILS') ?>
                    <i class="icon icon-ok"></i> 
                  </a>
                  <a class="btn" href="<?php echo JRoute::_('index.php?option=com_helloworld&view=images&id=' . (int) $item->id) ?>">
                    <?php echo Jtext::_('IMAGE_GALLERY') ?>
                    <i class="icon icon-ok"></i> 
                  </a>
                  <a class="btn" href="<?php echo JRoute::_('index.php?option=com_helloworld&task=availability.edit&id=' . (int) $item->id) ?>">
                    <?php echo Jtext::_('COM_HELLOWORLD_SUBMENU_MANAGE_AVAILABILITY') ?>
                    <i class="icon icon-ok"></i> 
                  </a>
                  <a class="btn" href="<?php echo JRoute::_('index.php?option=com_helloworld&task=tariffs.edit&id=' . (int) $item->id) ?>">
                    <?php echo Jtext::_('COM_HELLOWORLD_SUBMENU_MANAGE_TARIFFS') ?>
                    <i class="icon icon-ok"></i> 
                  </a>
                </td>
              </tr>
            <?php else : ?>
            <p>asasd</p>
          <?php endif; ?>
        <?php endforeach; ?>
        <input type="hidden" name="extension" value="<?php echo 'com_helloworld'; ?>" />

        </tbody>

        <tfoot>
          <tr>
            <td colspan="7"></td>
          </tr>
        </tfoot>
      </table>

      <?php echo $this->pagination->getListFooter(); ?>

      <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
        <?php echo JHtml::_('form.token'); ?>
      </div>
    </div>
</form>


