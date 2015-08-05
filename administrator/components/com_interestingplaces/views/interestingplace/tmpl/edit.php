<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// load tooltip behavior
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidator');
?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_interestingplaces&view=interestingplace&layout=edit&id=' . (int) $this->item->id); ?>" id="adminForm" method="post" name="adminForm">
  <div class="form-inline form-inline-header">
    <?php
    echo $this->form->getControlGroup('title');
    echo $this->form->getControlGroup('alias');
    ?>
  </div>
  <div class="row-fluid">
    <div class="span9">
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('description'); ?>
      </fieldset>
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('location'); ?>
      </fieldset>
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('public_transport'); ?>
      </fieldset>
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('general_facilities'); ?>
      </fieldset>
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('accessibility'); ?>
      </fieldset>
    </div>
    <div class="span3">
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('published'); ?>
      </fieldset>
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('date_created'); ?>
      </fieldset>
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('website'); ?>
      </fieldset>
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('email'); ?>
      </fieldset>
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('telephone'); ?>
      </fieldset>
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('latitude'); ?>
      </fieldset>
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('longitude'); ?>
      </fieldset>
      <fieldset class="adminform">
        <?php echo $this->form->getControlGroup('department'); ?>
      </fieldset>

    </div>
  </div>

  <input type="hidden" name="task" value="interestingplace.edit" />

  <?php echo JHtml::_('form.token'); ?>
</form>
