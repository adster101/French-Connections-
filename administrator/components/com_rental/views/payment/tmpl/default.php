<?php
// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$language = JFactory::getLanguage();
$language->load('plg_user_profile_fc', JPATH_ADMINISTRATOR, 'en-GB', true);

$total = '';
$total_vat = '';
$route = JRoute::_('index.php?option=com_rental&view=listing&id=' . (int) $this->id);
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
      <p>
        <?php echo JText::_('COM_RENTAL_HELLOWORLD_RENEWAL_PAYMENT_SUMMARY_BLURB'); ?>
      </p>
      <?php $this->payment_summary = new JLayoutFile('payment_summary', $basePath = JPATH_ADMINISTRATOR . '/components/com_rental/layouts'); ?>

      <?php echo $this->payment_summary->render($this->summary); ?>      

      <a href="<?php echo JRoute::_('index.php?option=com_rental&view=payment&layout=payment&id=' . (int) $this->id) ?>" class="btn btn-primary btn-large">
        Pay now using our secure server
        <i class="icon icon-white icon-next">&nbsp;</i>
      </a>
      <hr />
      <?php echo JText::sprintf('COM_RENTAL_HELLOWORLD_RENEWAL_PAYMENT_SUMMARY_HELP', $route); ?>

      <p>
        <img src="/images/general/sage_pay_logo.gif" alt="Sage pay logo" />
        <img src="/images/general/mcsc_logo.gif" alt="Sage pay logo" />
        <img src="/images/general/vbv_logo_small.gif" alt="Sage pay logo" />
      </p>
      <div class="span2">

      </div>

    </div>
  </div>
</div>