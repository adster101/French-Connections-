<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

?>
<form class="form-validate" action="<?php echo JRoute::_('index.php?option=com_helloworld&view=offer&task=edit&id=' . $this->item->id . '&offer_id=' .$this->item->offer_id ); ?>" id="offer-form" method="post" name="adminForm">
		<div class="width-60 fltlft">
      <fieldset class="adminform">
        <legend><?php echo JText::_( 'COM_HELLOWORLD_HELLOWORLD_OFFER_DETAILS' ); ?></legend>
        <ul class="adminformlist">
  <?php foreach($this->form->getFieldset('offer-details') as $field): ?>
          <li><?php echo $field->label;echo $field->input;?></li>
  <?php endforeach; ?>
        </ul>
      </fieldset>
    </div>
  <div class="width-40 fltrt">
		<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_HELLOWORLD_HELLOWORLD_OFFER_DETAILS_DATE' ); ?></legend>
			<ul class="adminformlist">
        <?php foreach($this->form->getFieldset('availability') as $field): ?>
          <li><?php echo $field->label;echo $field->input;?></li>
        <?php endforeach; ?>
			</ul>
		</fieldset>
	</div>
		<input type="hidden" name="task" value="offer.edit" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />

		<?php echo JHtml::_('form.token'); ?>
</form>
