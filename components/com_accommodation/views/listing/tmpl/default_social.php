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


<?php if (!empty($search_url)) : ?>
  <a class="" href="<?php echo $search_url ?>" title="">    
    <span class="glyphicon glyphicon-circle-arrow-left"></span>
    <?php echo JText::_('COM_ACCOMMODATION_BACK_TO_SEARCH_RESULTS'); ?>
  </a>
<?php else: ?>
  <a class="lead" href="<?php echo JRoute::_('index.php?option=com_fcsearch&Itemid=165&s_kwds=' . JApplication::stringURLSafe(JText::_('COM_ACCOMMODATION_SEARCH_FRANCE'))) ?>" title="">    
    <span class="glyphicon glyphicon-circle-arrow-left"></span>
    <?php echo JText::_('COM_ACCOMMODATION_BROWSE_ACCOMMODATION'); ?>
  </a>
<?php endif; ?>
<?php if ($logged_in) : ?>
  <a class="btn btn-default shortlist <?php echo ($action == 'add') ? 'muted' : '' ?>" data-animation="false" data-placement="left" data-toggle="popover" data-id='<?php echo $this->item->unit_id ?>' data-action='<?php echo $action ?>' href="<?php echo $uri ?>">
    <span class="glyphicon glyphicon-heart"></span>
    <?php echo JText::_('COM_ACCOMMODATION_SHORTLIST') ?>
  </a>
<?php else : ?>
  <button class="btn btn-default login" href="<?php echo $uri ?>">
    <span class="glyphicon glyphicon-heart muted"></span>
    <?php echo JText::_('COM_ACCOMMODATION_SHORTLIST') ?>
  </button>    
<?php endif; ?>
<div class="pull-right .icon-xxlarge">
  <span class="glyphicon icon-facebook"></span> 
  <span class="glyphicon icon-twitter"></span>
  <spab class="glyphicon icon-google-plus"></span>
</div>
<?php echo $modal->render($this->item); ?>

