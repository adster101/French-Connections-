<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

$uri = JUri::current();

$search_url = $app->getUserState('user.search');
?>
<div class="row-fluid social-row">
  <?php if (!empty($search_url)) : ?>
    <div class="span3">
      <a class="btn" href="<?php echo $search_url ?>" title="">    
        <i class="icon icon-backward-2"></i>
        <?php echo JText::_('COM_ACCOMMODATION_BACK_TO_SEARCH_RESULTS'); ?>
      </a>
    </div>
    <div class="span9">
          <p class="addthis_default_style pull-right">
            <!-- AddThis Button BEGIN -->
            <a class="addthis_button_print " title="Print" href="#"></a>
            <a class="addthis_button_facebook " title="Send to Facebook" href="#"></a>
            <a class="addthis_button_twitter " title="Tweet This" href="#"></a>
            <a class="addthis_button_email " title="Email" href="#"></a>
            <a class="addthis_button_compact" href="#"></a>          
            <!-- AddThis Button END -->	
          </p>   
    </div>
  <?php else: ?>

  <?php endif; ?>
</div>

