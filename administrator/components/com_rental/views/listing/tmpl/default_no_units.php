<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('dropdown.init');


$listDirn = $this->escape($this->state->get('list.direction'));
$listOrder = $this->escape($this->state->get('list.ordering'));


$user = JFactory::getUser();
$userId = $user->get('id');
$groups = $user->getAuthorisedGroups();
$ordering = ($listOrder == 'a.lft');
$originalOrders = array();

$canDo = RentalHelper::getActions();
?>

<form action="<?php echo JRoute::_('index.php?option=com_rental'); ?>" method="post" name="adminForm" class="form-validate" id="adminForm">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
      <div id="j-main-container">
      <?php endif; ?>
      <?php if ($canDo->get('core.edit.state')) : ?>
        <div class="js-stools-container-filters hidden-phone clearfix">
          <?php
          $data['view'] = $this;
          echo JLayoutHelper::render('joomla.searchtools.default.filters', $data);
          ?>
        </div>

      <?php endif; ?>
      <hr />
      <div class="alert alert-block">
        <strong>
          <?php echo JText::_('COM_RENTAL_HELLOWORLD_NO_LISTINGS'); ?>
        </strong>
      </div>
      <hr/>
      <input type="hidden" name="extension" value="<?php echo 'com_rental'; ?>" />

      <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="" />
        <?php echo JHtml::_('form.token'); ?>
      </div>
    </div>
</form>