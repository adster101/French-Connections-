<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$user = JFactory::getUser();
$action = 'add';
$uri = JUri::current();

$search_url = $app->getUserState('user.search');
?>
<style>
  .affix {
    position:fixed;
    top:0;
    background:#fff;
    left:0;
    right:0;
    z-index:1000;
  }
  .affix .social-row {
    padding: 0 18px;
    box-shadow: 0px 4px 4px -2px rgba(0, 0, 0, 0.2);
  }

  .affix .social-row-inner {
    padding: 6px 18px 6px 18px;
  }
  .social-item {
    padding:9px;
  }
</style>
<div>
  <div class="social-row clearfix">
    <?php if (!empty($search_url)) : ?>
      <div class="social-row-inner">
        <a class="btn btn-small" href="<?php echo $search_url ?>" title="">    
          <i class="icon icon-backward-2"></i>
          <?php echo JText::_('COM_ACCOMMODATION_BACK_TO_SEARCH_RESULTS'); ?>
        </a>
        <?php endif; ?>
        <div class="dropdown pull-right">
          <a class="dropdown-toggle btn btn-small" id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="#">
            <i class="icon icon-facebook"></i>
            <i class="icon icon-twitter"></i>
            <i class="icon icon-google-plus"></i>
            <?php echo JText::_('COM_ACCOMMODATION_SHARE') ?>
          </a> 
          <!-- Link or button to toggle dropdown -->
          <div class="dropdown-menu" role="menu" aria-labelledby="dLabel">
            <div class="social-item"> 
              <div id="fb-root"></div>
              <div class="fb-like" data-href="<?php echo JFactory::getURI()->current() ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>          </div>
            <div class="social-item">  
              <a href="https://twitter.com/share" class="twitter-share-button" data-url="https://dev.twitter.com" data-via="your_screen_name" data-lang="en">Tweet</a>          </div>
          </div>
        </div>
        <a class="btn btn-small pull-right shortlist <?php echo ($action == 'add') ? 'muted' : '' ?>" data-animation="false" data-placement="bottom" data-toggle="popover" data-id='<?php echo $this->item->unit_id ?>' data-action='<?php echo $action ?>' href="#">
          <i class="icon icon-heart"></i>
          <?php echo JText::_('COM_ACCOMMODATION_SHORTLIST') ?>
        </a>

      </div>
    </div>
</div>

