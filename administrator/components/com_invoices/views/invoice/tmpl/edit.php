<?php
/**
 * @version     1.0.0
 * @package     com_invoices
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_invoices/assets/css/invoices.css');
?>
<script type="text/javascript">
    
    
    Joomla.submitbutton = function(task)
    {
        if(task == 'invoice.cancel'){
            Joomla.submitform(task, document.getElementById('invoice-form'));
        }
        else{
            
            if (task != 'invoice.cancel' && document.formvalidator.isValid(document.id('invoice-form'))) {
                Joomla.submitform(task, document.getElementById('invoice-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_invoices&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="invoice-form" class="form-validate">
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">

                			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('user_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('user_id'); ?></div>
			</div>
				<input type="hidden" name="jform[date_created]" value="<?php echo $this->item->date_created; ?>" />
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('currency'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('currency'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('exchange_rate'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('exchange_rate'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('invoice_type'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('invoice_type'); ?></div>
			</div>
				<input type="hidden" name="jform[journal_memo]" value="<?php echo $this->item->journal_memo; ?>" />
				<input type="hidden" name="jform[total_net]" value="<?php echo $this->item->total_net; ?>" />
				<input type="hidden" name="jform[vat]" value="<?php echo $this->item->vat; ?>" />
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('property_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('property_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('due_date'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('due_date'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('salutation'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('salutation'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('first_name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('first_name'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('surname'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('surname'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('address'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('address'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('town'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('town'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('county'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('county'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('postcode'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('postcode'); ?></div>
			</div>


            </fieldset>
        </div>

        

        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>