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



        <div class="well well-small">
          <?php echo JText::_('COM_RENTAL_HELLOWORLD_LISTING_SUBMISSION_BLURB'); ?>
          <hr />
          <fieldset class="panelform">
            <div class="control-group">   
              <?php echo $this->form->getLabel('admin_notes'); ?>
              <div class="controls">   
                <?php echo $this->form->getInput('admin_notes'); ?>
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
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="property_id" value="<?php echo (int) $this->id ?>" />

        <?php echo JHtml::_('form.token'); ?> .
      </form>
    </div>
  </div>


