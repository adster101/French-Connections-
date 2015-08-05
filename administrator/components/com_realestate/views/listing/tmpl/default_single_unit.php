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

$user = JFactory::getUser();
$userId = $user->get('id');
$groups = $user->getAuthorisedGroups();
$ordering = ($listOrder == 'a.lft');
$originalOrders = array();

$canDo = PropertyHelper::getActions();
$canEditOwn = $canDo->get('core.edit.own');
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
      <form action="<?php echo JRoute::_('index.php?option=com_realestate&id=' . (int) $this->id); ?>" method="post" name="adminForm" class="form-validate form-vertical" id="adminForm">
        <div class="well well-small">
          <?php echo JText::_('COM_RENTAL_HELLOWORLD_LISTING_SUBMISSION_BLURB'); ?>
          <hr />
          <fieldset class="panelform">
            <div class="control-group">   
              <?php echo $this->form->getLabel('body'); ?>
              <div class="controls">   
                <?php echo $this->form->getInput('body'); ?>
              </div>
            </div>      
            <?php echo $this->form->getInput('tos'); ?>  
            <?php echo $this->form->getLabel('tos'); ?>
            <hr />
          </fieldset>
          <button class="btn btn-primary" onclick="Joomla.submitbutton('listing.submit')">
            <?php echo JText::_('COM_RENTAL_HELLOWORLD_LISTING_SUBMIT_FOR_REVIEW_BUTTON'); ?>
          </button>
        </div>
        <?php echo $this->form->getInput('id'); ?>  
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?> .
      </form>
    </div>
  </div>


