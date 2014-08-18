<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$user = JFactory::getUser();
$uri = JUri::getInstance()->toString();
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
    <div class="pull-right">
      <?php if ($logged_in) : ?>
        <a class="btn btn-default shortlist <?php echo ($action == 'add') ? 'muted' : '' ?>" data-animation="false" data-placement="left" data-toggle="popover" data-id='<?php echo $this->item->unit_id ?>' data-action='<?php echo $action ?>' href="<?php echo $uri ?>">
          <span class="glyphicon glyphicon-heart"></span>
          <?php echo JText::_('COM_ACCOMMODATION_SHORTLIST') ?>
        </a>
      <?php else : ?>
        <button class="btn btn-small login" href="<?php echo $uri ?>">
          <span class="glyphicon glyphicon-heart muted"></span>
          <?php echo JText::_('COM_ACCOMMODATION_SHORTLIST') ?>
        </button>    
      <?php endif; ?>
      &nbsp;
      <i class="icon lead icon-facebook"></i> 
      <i class="icon lead icon-twitter"></i>
      <i class="icon lead icon-google-plus"></i>
    </div>
  </div>
</div>

<?php echo $modal->render($this->item); ?>

