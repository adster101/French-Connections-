<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');

$fieldsets = $this->form->getFieldSets();
$total = '';
$total_vat = '';
?>
<div class="row-fluid">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="" class="span8">
    <?php else : ?>
      <div class="span10">
      <?php endif; ?>
      <h2>
        <?php echo JText::_('COM_RENTAL_HELLOWORLD_RENEWAL_PAYMENT_SUMMARY_TITLE'); ?>
      </h2>
      <p>
        <?php echo JText::_('COM_RENTAL_HELLOWORLD_RENEWAL_PAYMENT_SUMMARY_BLURB'); ?>
      </p>
      <?php if (!empty($this->summary)) : ?>
        <?php $this->payment_summary = new JLayoutFile('payment_summary', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts'); ?>

        <?php echo $this->payment_summary->render($this->summary); ?>      
        <form autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_rental&option=com_rental&view=renewal&layout=payment&id=' . (int) $this->id) ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
          <?php foreach ($fieldsets as $fieldset) : ?>
            <fieldset id="<?php echo $fieldset->id ?>">
              <legend><?php echo JText::_($fieldset->label); ?></legend>
              <p><?php echo JText::_($fieldset->description); ?></p>
              <?php foreach ($this->form->getFieldset($fieldset->name) as $field) : ?>
                <div class="control-group">
                  <?php echo $field->label; ?>
                  <div class="controls">
                    <?php echo $field->input; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </fieldset>
          <?php endforeach; ?>
          <div class="control-group">
              <?php echo $this->form->getLabel('tsandcs'); ?>
            <div class="controls">
              <?php echo $this->form->getInput('tsandcs'); ?>
            </div>
          </div> 
          <button class="btn btn-primary btn-large payment-button" onclick="Joomla.submitbutton('payment.process')" data-loading-text="Processing payment...">
            <span class="icon icon-next">&nbsp;</span>&nbsp;Submit payment</span>
          </button>
          <hr />
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="renewal" value="<?php echo $this->renewal; ?>" />

          <?php echo JHtml::_('form.token'); ?>
          <?php echo $this->form->getInput('id'); ?>

        </form>
      <?php else: ?>
        <div class="alert alert-info">
          <?php JText::_('COM_RENTAL_PAYMENT_NO_PAYMENT_DUE'); ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="span2">
      <p>
        <img src="/images/general/sage_pay_logo.gif" alt="Sage pay logo" />
        <img src="/images/general/mcsc_logo.gif" alt="Sage pay logo" />
        <img src="/images/general/vbv_logo_small.gif" alt="Sage pay logo" />
      </p>
    </div>
