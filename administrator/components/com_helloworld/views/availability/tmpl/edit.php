<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$data = JApplication::getUserState('listing', '');
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=availability&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">
  <div class="row-fluid">
    <?php if (!empty($this->sidebar)): ?>
      <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
      </div>
      <div id="j-main-container" class="span8">
      <?php else : ?>
        <div id="j-main-container" class="span10">
        <?php endif; ?>
        <?php
          $layout = new JLayoutFile('accommodation_tabs', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts');
          echo $layout->render($data);
        ?>  
          
        <fieldset class="adminform">
          <legend><?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_AVAILABILITY', $this->item->unit_title); ?></legend>
          <p>
            <?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_AVAILABILITY_LAST_UPDATED',$this->item->availability_last_updated); ?>
          </p>
          <?php echo $this->calendar; ?>
        </fieldset>
        <fieldset class="adminform">
          <legend><?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_UPDATE_AVAILABILITY'); ?></legend>

            <?php foreach ($this->form->getFieldset('availability') as $field): ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div></div>
          <?php endforeach; ?>
        </fieldset>
          
        <input type="hidden" name="task" value="availability.edit" />
        <?php echo JHtml::_('form.token'); ?>
        </form>
      </div>
      <div class="span2">
        <div class="well well-small">
          <h3>Availability calendar</h3>
          <ul>
            <li>Update yo availability, fool!</li>
          </ul>
        </div>
      </div>
    </div>
