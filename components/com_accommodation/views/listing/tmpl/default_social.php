<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$user = JFactory::getUser();
$uri = JUri::current();
$logged_in = ($user->guest) ? false : true;
$action = (array_key_exists($this->item->unit_id, $this->shortlist)) ? 'remove' : 'add';
$search_url = $app->getUserState('user.search');


// The layout for the anchor based navigation on the property listing
$modal = new JLayoutFile('shortlist_modal', $basePath = JPATH_SITE . '/components/com_accommodation/layouts');
?>

<div class="row-fluid">
  <div class="social-row clearfix">
    <?php if (!empty($search_url)) : ?>
      <a class="btn btn-small pull-left" href="<?php echo $search_url ?>" title="">    
        <i class="icon icon-backward-2"></i>
        <?php echo JText::_('COM_ACCOMMODATION_BACK_TO_SEARCH_RESULTS'); ?>
      </a>
    <?php else: ?>
      <a class="btn btn-small pull-left" href="<?php echo JRoute::_('index.php?option=com_fcsearch&Itemid=165&s_kwds=' . JApplication::stringURLSafe(JText::_('COM_ACCOMMODATION_SEARCH_FRANCE'))) ?>" title="">    
        <i class="icon icon-backward-2"></i>
        <?php echo JText::_('COM_ACCOMMODATION_BROWSE_ACCOMMODATION'); ?>
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
          <div class="fb-like" data-href="<?php echo htmlspecialchars($uri) ?>" data-layout="button_count" data-action="like" data-show-faces="false" data-share="true"></div>         
        </div>
        <div class="social-item">  
          <a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo htmlspecialchars($uri) ?>" data-via="your_screen_name" data-lang="en">Tweet</a>       
        </div>
      </div>
    </div>
    <?php if ($logged_in) : ?>
      <a class="btn btn-small pull-right shortlist <?php echo ($action == 'add') ? 'muted' : '' ?>" data-animation="false" data-placement="left" data-toggle="popover" data-id='<?php echo $this->item->unit_id ?>' data-action='<?php echo $action ?>' href="#">
        <i class="icon-heart"></i>
        <?php echo JText::_('COM_ACCOMMODATION_SHORTLIST') ?>
      </a>
    <?php else : ?>
      <a class="btn btn-small pull-right login" href="#">
        <i class="icon-heart muted"></i>
        <?php echo JText::_('COM_ACCOMMODATION_SHORTLIST') ?>
      </a>    
    <?php endif; ?>
  </div>
</div>

<?php echo $modal->render($this->item); ?>

