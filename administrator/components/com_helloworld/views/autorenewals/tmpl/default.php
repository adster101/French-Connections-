<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$language = JFactory::getLanguage();

$fieldsets = $this->form->getFieldSets();
?>
<div class="row-fluid">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
    </div>
    <div id="" class="span8">
    <?php else : ?>
      <div lass="span10">
      <?php endif; ?> 
      <form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=autorenewals&id=' . (int) $this->id) ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
        <?php foreach ($fieldsets as $fieldset) : ?>
          <fieldset>
            <?php echo JText::_($fieldset->description); ?>
            <legend><?php echo JText::_($fieldset->label); ?></legend>
            <?php foreach ($this->form->getFieldset($fieldset->name) as $field) : ?>
              <div class="control-group">
                <?php echo $field->label; ?>
                <div class="controls">
                  <?php echo $field->input; ?>
                </div> 
              </div>
              <hr />
            <?php endforeach; ?>
          </fieldset>           
        <?php endforeach; ?>



        <input type="hidden" name="task" value="" />
        <input type="hidden" name="jform[id]" value="11" />
        
        <?php echo JHtml::_('form.token'); ?>


      </form>

    </div>

    <div class="span2">

      <h4>Secure details</h4>
      <p>Choosing to pay now using our secure payment form will ensure your property is published as soon as possible. All major CREDIT or DEBIT cards are accepted (UK and international) on our completely secure server.</p>
      <p><img src="/images/general/sage_pay_logo.gif" alt="Sage pay logo" />
      <p><img src="/images/general/mcsc_logo.gif" alt="Sage pay logo" />
      <p><img src="/images/general/vbv_logo_small.gif" alt="Sage pay logo" />
    </div>
