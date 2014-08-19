<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$lang = $app->input->get('lang', 'en');
$user = JFactory::getUser();
$uri = JUri::getInstance()->toString();
$logged_in = ($user->guest) ? false : true;
$action = (array_key_exists($this->item->unit_id, $this->shortlist)) ? 'remove' : 'add';
$search_url = $app->getUserState('user.search');
$Itemid = FCSearchHelperRoute::getItemid(array('component', 'com_fcsearch'));

// The layout for the anchor based navigation on the property listing
$modal = new JLayoutFile('shortlist_modal', $basePath = JPATH_SITE . '/components/com_accommodation/layouts');

$offset = (empty($search_url)) ? 'col-md-offset-6 col-lg-offset-8 col-sm-offset-3' : 'col-lg-offset-6 col-md-offset-3 col-sm-offset-1 col-xs-offset-0';
?>
<div class="row">
  <?php if (!empty($search_url)) : ?>
    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-12">
      <a class="btn btn-primary" href="<?php echo $search_url ?>" title="">    
        <span class="glyphicon glyphicon-circle-arrow-left"></span>
        <?php echo JText::_('COM_ACCOMMODATION_BACK_TO_SEARCH_RESULTS'); ?>
      </a>
    </div>
  <?php endif; ?>
  <div class="col-lg-4 col-md-6 col-sm-8 col-xs-12 <?php echo $offset ?>">
    <div class="visible-lg-inline-block visible-md-inline-block visible-sm-inline-block visible-xs-inline-block">
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
    </div>
    <div class="icon-xxlarge visible-lg-inline-block visible-md-inline-block visible-sm-inline-block visible-xs-inline-block"> 
      <span class="glyphicon social-icon icon-facebook"></span> 
      <span class="glyphicon social-icon icon-twitter"></span>
      <span class="glyphicon social-icon icon-google-plus"></span>
    </div>

    <div class="visible-lg-inline-block visible-md-inline-block visible-sm-inline-block hidden-xs">
      <form class="form-inline" id="property-search" method="POST" action="<?php echo JRoute::_('index.php?option=com_fcsearch&lang=' . $lang . '&Itemid=' . (int) $Itemid . '&s_kwds=' . JText::_('COM_FCSEARCH_S_KWDS_DEFAULT')) ?>">
        <label class="sr-only" for="q">
          <?php echo JText::_('COM_FCSEARCH_SEARCH_QUERY_LABEL'); ?>
        </label>
        <input id="s_kwds" class="typeahead search-box form-control" type="text" name="s_kwds" autocomplete="Off" value="" placeholder="<?php echo JText::_('COM_ACCOMMODATION_SEARCH_DESTINATION_OR_PROPERTY') ?>" />
        <button class="property-search-button btn btn-primary">
          <span class="glyphicon glyphicon-search"><span class="sr-only"><?php echo JText::_('JSEARCH') ?></span></span>
        </button>
      </form>
    </div>
  </div>

</div>
<?php echo $modal->render($this->item); ?>

