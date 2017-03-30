<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');



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
                <?php echo JText::_('COM_RENTAL_HELLOWORLD_RENEWAL_PAYMENT_SUMMARY_TITLE'); ?>
            </h2>

            <?php //$this->payment_summary = new JLayoutFile('payment_summary', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts'); ?>

            <?php //echo $this->payment_summary->render($this->summary); ?>   
            <div class="alert alert-notice">
                <?php echo JText::_('COM_RENTAL_HELLOWORLD_RENEWAL_PAYMENT_VAT_STATUS_BLURB'); ?>       
            </div>
            <form action="<?php echo JRoute::_('index.php?option=com_realestate&view=payment&layout=payment&id=' . (int) $this->id) ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-vertical">
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
                <fieldset>
                    <legend><?php echo JText::_('Address details'); ?></legend>

                    <?php foreach ($this->form->getFieldset('address') as $field) : ?>                
                        <div class="control-group">
                            <div class="control-label"> 
                                <?php echo $field->label; ?>

                            </div>
                            <div class="controls">   
                                <?php echo $field->input; ?>

                            </div>
                        </div>    
                    </fieldset>

                <?php endforeach; // End of foreach getFieldSet fieldset name   ?>  
                <hr />

                <?php echo JHtmlProperty::button('btn btn-primary btn-large', 'listing.accountupdate', 'icon-next', 'COM_RENTAL_UPDATE_ACCOUNT_DETAILS_AND_PROCEED'); ?>

                <input type="hidden" name="task" value="" />
                <?php echo JHtml::_('form.token'); ?>
                <?php echo $this->form->getInput('property_id'); ?>

                <hr />
                <?php echo JText::_('COM_RENTAL_HELLOWORLD_RENEWAL_PAYMENT_SUMMARY_HELP'); ?>

                <p>
                    <img src="/images/general/sage_pay_logo.gif" alt="Sage pay logo" />
                    <img src="/images/general/mcsc_logo.gif" alt="Sage pay logo" />
                    <img src="/images/general/vbv_logo_small.gif" alt="Sage pay logo" />
                </p>
        </div>

        <div class="span2">

        </div>
