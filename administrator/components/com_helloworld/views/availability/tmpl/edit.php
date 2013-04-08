<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$data = JApplication::getUserState('listing', '');
$availability_last_updated = ($this->item->availability_last_updated) ? $this->item->availability_last_updated : '';
?>
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
      <div class="row-fluid">
        <div class="span8">
          <p class="pull-left">
            <?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_AVAILABILITY_LAST_UPDATED', $availability_last_updated); ?>
          </p>
        </div>
        <div class="span4">
          <table class="key">
            <tr>
              <td class="available"></td>
              <td>&nbsp;<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_AVAILABILITY_AVAILABLE') ?></td>
              <td>&nbsp;</td>
              <td class="unavailable">1</td>
              <td>&nbsp;<?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_AVAILABILITY_UNAVAILABLE') ?></td>
          </table> 
        </div>
      </div>
      <legend><?php echo JText::sprintf('COM_HELLOWORLD_HELLOWORLD_AVAILABILITY', $this->item->unit_title); ?></legend>
      <?php echo $this->calendar; ?>
      <form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=availability&task=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
        <div id="availabilityModal" class="hide fade modal">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">
              <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_UPDATE_AVAILABILITY') ?>
            </h3>
          </div>
          <div class="modal-body">
            <fieldset class="adminform">
              <?php foreach ($this->form->getFieldset('availability') as $field): ?>
                <div class="control-group">
                  <?php echo $field->label; ?>
                  <div class="controls">
                    <?php echo $field->input; ?>
                  </div></div>
              <?php endforeach; ?>
            </fieldset>
            <input type="hidden" name="task" value="availability.apply" />
            <?php echo JHtml::_('form.token'); ?>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary">
              <?php echo JText::_('JSAVE') ?>
            </button>
            <button class="btn" data-dismiss="modal" aria-hidden="true">
              <?php echo JText::_('JCANCEL') ?>
            </button>

          </div> 
        </div>
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
