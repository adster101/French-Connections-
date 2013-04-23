<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$language = JFactory::getLanguage();
$language->load('plg_user_profile_fc', JPATH_ADMINISTRATOR, 'en-GB', true);
?>
<div class="row-fluid">
  <?php if (!empty($this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
      <?php echo $this->sidebar; ?>
      <!--
        <script src="http://help.frenchconnections.co.uk/ChatScript.ashx?config=1&id=ControlID" type="text/javascript"></script>
      -->
    </div>
    <div id="" class="span8">
    <?php else : ?>
      <div lass="span10">
      <?php endif; ?> 
      <form action="<?php echo JRoute::_('index.php?option=com_helloworld&view=renewal&layout=billing&id=' . (int) $this->id) ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
        <fieldset>
          <legend><?php echo JText::_('PLG_USER_PROFILE_LEGEND_BILLING_DETAILS'); ?></legend>
          <?php foreach ($this->form->getFieldset('address') as $field) : ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>
            <hr />
          <?php endforeach; ?>
        </fieldset>
        <fieldset>
          <legend><?php echo JText::_('PLG_USER_PROFILE_LEGEND_BILLING_DETAILS'); ?></legend>
          <?php foreach ($this->form->getFieldset('vat-details') as $field) : ?>
            <div class="control-group">
              <?php echo $field->label; ?>
              <div class="controls">
                <?php echo $field->input; ?>
              </div>
            </div>
            <hr />
          <?php endforeach; ?>
        </fieldset>        
        <button class="btn btn-primary pull-right">
          Proceed >>
        </button>

        <input type="hidden" name="task" value="renewal.doPayment" />
        <?php echo JHtml::_('form.token'); ?>


      </form>

    </div>

