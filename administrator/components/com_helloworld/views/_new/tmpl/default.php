<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

if ($this->items > 0) :
  ?>
  <form method="post" name="adminForm" id="adminForm" class="form-validate" action="<?php echo JRoute::_('index.php?option=com_helloworld&task=helloworld.addnew') . '&' . JSession::getFormToken() . '=1'; ?>">

    <div class="pre_message">
      <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_SECOND_NEW_PROPERTY_BLURB'); ?>
    </div>
    <fieldset class="adminform">
      <h4><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_SECOND_NEW_PROPERTY_LEGEND'); ?></h4>
      <?php foreach ($this->form->getFieldset('properties') as $field): ?>

        <div class="control-group">
          <div class="controls">
            <?php
            echo $field->label;
            echo $field->input;
            ?>
          </div>
        </div>
  <?php endforeach; ?>
    </fieldset>


    <hr />
    <button class="btn btn-large btn-primary" onclick="Joomla.submitbutton('helloworld.addnew')" >
  <?php echo JText::_('COM_HELLOWORLD_NEW_PROPERTY_PROCEED'); ?>
      <i class="icon-next ">
      </i>
    </button>	
  <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="" />
  </form>
<?php else : ?>

  <div class="pre_message">
  <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_CREATING_FIRST_PROPERTY_BLURB'); ?>
  </div>
  <hr />
  <a class="btn btn-primary" href="index.php?option=com_helloworld&task=helloworld.edit">
  <?php echo JText::_('COM_HELLOWORLD_NEW_PROPERTY_PROCEED'); ?>
    <i class="boot-icon-forward boot-icon-white"></i>
  </a>
<?php endif; ?>

