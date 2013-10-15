<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_admin
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');


JHtml::_('behavior.formvalidation');


$lang = JFactory::getLanguage();
$lang->load('com_invoices', JPATH_ADMINISTRATOR, null, false, true);

// Register and load the invoices submenu heper
JLoader::register('InvoicesHelper', JPATH_ADMINISTRATOR . '/components/com_invoices/helpers/invoices.php');
InvoicesHelper::addSubmenu('account');

// Get the form fieldsets.
$fieldsets = $this->form->getFieldsets();

$company_number = $this->form->getValue('company_number');
$vat_status = $this->form->getValue('vat_status');

$show_vat_number = ($this->item->vat_status == 'ECS') ? true : false;
$show_company_number = (!empty($company_number) && $vat_status == 'ZA') ? true : false;
?>

<script type="text/javascript">
  Joomla.submitbutton = function(task)
  {
    if (task == 'profile.cancel' || document.formvalidator.isValid(document.id('profile-form')))
    {
      Joomla.submitform(task, document.getElementById('profile-form'));
    }
  }





</script>

<form action="<?php echo JRoute::_('index.php?option=com_admin&view=profile&layout=edit&id=' . $this->item->id); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" enctype="multipart/form-data">
  <div class="row-fluid">
    <div id="j-sidebar-container" class="span2">
      <?php echo JHtmlSidebar::render(); ?>
    </div>
    <div id="" class="span10">
      <fieldset>
        <legend>Your name</legend>
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('firstname'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('firstname'); ?>
          </div>
        </div>
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('surname'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('surname'); ?>
          </div>
        </div>        
      </fieldset>
      <fieldset>
        <legend>Login Details</legend>
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('username'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('username'); ?>
          </div>
        </div>            
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('password2'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('password2'); ?>
          </div>
        </div>  
                <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('password'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('password'); ?>
          </div>
        </div>  
      </fieldset>
      <fieldset>
        <legend>Contact Details</legend>
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('email'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('email'); ?>
          </div>
        </div>
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('phone_1'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('phone_1'); ?>
          </div>
        </div>
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('phone_2'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('phone_2'); ?>
          </div>
        </div>       
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('phone_3'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('phone_3'); ?>
          </div>
        </div>               
      </fieldset>
      <fieldset>
        <legend>VAT Status</legend>
        <?php echo $this->form->getLabel('vat_status_note'); ?>

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
        <legend>Invoice Address</legend>
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('address1'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('address1'); ?>
          </div>
        </div>            
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('address2'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('address2'); ?>
          </div>
        </div>   
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('city'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('city'); ?>
          </div>
        </div>   
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('region'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('region'); ?>
          </div>
        </div>   
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('country'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('country'); ?>
          </div>
        </div> 
        <div class="control-group">
          <div class="control-label">
            <?php echo $this->form->getLabel('postal_code'); ?>
          </div>
          <div class="controls">
            <?php echo $this->form->getInput('postal_code'); ?>
          </div>
        </div> 
      </fieldset>
    </div>
  </div>

  <?php echo JHtml::_('bootstrap.endTabSet'); ?>
  <input type="hidden" name="task" value="" />
  <?php echo JHtml::_('form.token'); ?>
</form>
