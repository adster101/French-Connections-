<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$language = JFactory::getLanguage();
$language->load('plg_user_profile_fc', JPATH_ADMINISTRATOR, 'en-GB', true);
$fieldsets = $this->form->getFieldSets();

$total = '';
$total_vat = '';
$show_vat_number = '';
$show_company_number = '';
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
      <h2>
        <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_RENEWAL_PAYMENT_SUMMARY_TITLE'); ?>
      </h2>
      <p>
        <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_RENEWAL_PAYMENT_SUMMARY_BLURB'); ?>
      </p>
      <?php $this->payment_summary = new JLayoutFile('payment_summary', $basePath = JPATH_ADMINISTRATOR . '/components/com_helloworld/layouts'); ?>

      <?php echo $this->payment_summary->render($this->summary); ?>   
      <div class="alert alert-notice">
        <span class='icon icon-flag'> </span>
        Our records indicate that we don't have VAT or invoice details registered on our system. Please complete the following before proceeding.
      </div>
      <form action="<?php echo JRoute::_('index.php?option=com_helloworld&option=com_helloworld&task=renewal&layout=payment&id=' . (int) $this->id) ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
        <fieldset>
          <div class="control-group">
            <div class="control-label">
              <?php echo $this->form->getLabel('vat_status'); ?>
            </div>
            <div class="controls">
              <?php echo $this->form->getInput('vat_status'); ?>
            </div>
          </div>          
          <div id="vat_number" class="<?php echo ($show_vat_number) ? '' : 'hide' ?> "> 
            <div class="control-group">
              <div class="control-label">
                <?php echo $this->form->getLabel('vat_number'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('vat_number'); ?>
              </div>
            </div>
          </div>
          <div id="company_number" class="<?php echo ($show_company_number) ? '' : 'hide' ?> "> 
            <div class="control-group">
              <div class="control-label">
                <?php echo $this->form->getLabel('company_number'); ?>
              </div>
              <div class="controls">
                <?php echo $this->form->getInput('company_number'); ?>
              </div>
            </div>    
          </div>
        </fieldset>
        <?php echo JHtmlProperty::button('btn btn-primary btn-large pull-right', 'listing.accountupdate', 'icon-next', 'Proceed'); ?>

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
        <?php echo $this->form->getInput('property_id'); ?>
      
        <hr />
        <?php echo JText::_('COM_HELLOWORLD_HELLOWORLD_RENEWAL_PAYMENT_SUMMARY_HELP'); ?>

        <p>
          <img src="/images/general/sage_pay_logo.gif" alt="Sage pay logo" />
          <img src="/images/general/mcsc_logo.gif" alt="Sage pay logo" />
          <img src="/images/general/vbv_logo_small.gif" alt="Sage pay logo" />
        </p>
    </div>

    <div class="span2">

    </div>
