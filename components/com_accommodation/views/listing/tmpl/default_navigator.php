<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$inShortlist = (array_key_exists($this->item->unit_id, $this->shortlist)) ? 1 : 0;
$action = (array_key_exists($this->item->unit_id, $this->shortlist)) ? 'remove' : 'add';

$user = JFactory::getUser();
$logged_in = ($user->guest) ? false : true;
$HolidayMakerLogin = SearchHelper::getItemid(array('component', 'com_users'));
$uri = JUri::getInstance()->toString();

// Shortlist button thingy
$shortlist = new JLayoutFile('frenchconnections.general.shortlist');
$displayData = new StdClass;
$displayData->action = $action;
$displayData->inShortlist = $inShortlist;
$displayData->unit_id = $this->item->unit_id;
$displayData->class = ' btn btn-default';

?>
<div class="navbar-property-navigator" data-spy="affix" data-offset-top="640" >
  <div class="container">
    <div class="row">
      <div class="col-lg-10 col-md-9 col-sm-8 hidden-xs">
        <ul class="nav nav-pills">
          <li>
            <a href="<?php echo $this->route ?>#top">
              <span class="glyphicon glyphicon-home"></span>          
              <?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_SUMMARY'); ?>
            </a>
          </li>
          <li>
            <a href="<?php echo $this->route ?>#about">
              <span class="glyphicon glyphicon-info-sign"></span>          
              <?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_DESCRIPTION'); ?>
            </a>
          </li>
          <?php if (!empty($this->item->location_details)) : ?>
            <li>
              <a href="<?php echo $this->route ?>#location">
                <span class="glyphicon glyphicon-map-marker"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_LOCATION'); ?>
              </a>
            </li>
          <?php endif; ?>
          <?php if (!empty($this->item->getting_there)) : ?>
            <li>
              <a href="<?php echo $this->route ?>#gettingthere">
                <span class="glyphicon glyphicon-plane"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TRAVEL'); ?>
              </a>
            </li>
          <?php endif; ?>
          <?php if ($this->item->reviews && count($this->item->reviews) > 0) : ?>
            <li>
              <a href="<?php echo $this->route ?>#reviews">
                <span class="glyphicon glyphicon-power-cord "></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_REVIEWS'); ?>
              </a>
            </li>
          <?php endif; ?>
          <li>
            <a href="<?php echo $this->route ?>#availability">
              <span class="glyphicon glyphicon-calendar"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_AVAILABILITY'); ?>
            </a>
          </li>
          <li>
            <a href="<?php echo $this->route ?>#tariffs">
              <span class="glyphicon glyphicon-euro"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_TARIFFS'); ?>
            </a>
          </li>
          <li>
            <a href="<?php echo $this->route ?>#facilities">
              <span class="glyphicon glyphicon-th-list"></span>&nbsp;<?php echo JText::_('COM_ACCOMMODATION_NAVIGATOR_FACILITIES'); ?>
            </a>
          </li>
          <li>
            <a href="<?php echo $this->route ?>#email">
              <?php $contact_anchor_label = ($this->item->is_bookable) ? 'COM_ACCOMMODATION_NAVIGATOR_BOOK_NOW' : 'COM_ACCOMMODATION_NAVIGATOR_CONTACT'; ?>
              <span class="glyphicon glyphicon-envelope"></span>&nbsp;<?php echo JText::_($contact_anchor_label); ?>
            </a>
          </li>
        </ul>
      </div>
      <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="visible-lg-inline-block visible-md-inline-block visible-sm-inline-block visible-xs-inline-block">
          <?php if ($logged_in) : ?>
            <?php echo $shortlist->render($displayData); ?>
          <?php else : ?>
            <a class="btn btn-default" href="<?php echo JRoute::_('index.php?option=com_users&Itemid=' . (int) $HolidayMakerLogin) ?>">
              <span class="glyphicon glyphicon-heart muted"></span>
              <?php echo JText::_('COM_ACCOMMODATION_SHORTLIST') ?>
            </a>    
          <?php endif; ?>
        </div>
        <div class="glyphicon-xxlarge visible-lg-inline-block visible-md-inline-block visible-sm-inline-block visible-xs-inline-block"> 
          <a target="_blank" href="<?php
          echo 'https://www.facebook.com/dialog/feed?app_id=612921288819888&display=page&href='
          . urlencode($uri)
          . '&redirect_uri='
          . urlencode($uri)
          . '&picture='
          . JURI::root() . 'images/property/'
          . $this->item->unit_id
          . '/thumbs/'
          . urlencode($this->images[0]->image_file_name)
          . '&name=' . urlencode($this->item->unit_title)
          . '&description=' . urlencode(JHtml::_('string.truncate', $this->item->description, 100, true, false));
          ?>"
             <span class="glyphicon social-icon facebook"></span>
          </a> 
          <a target="_blank" href="<?php echo 'http://twitter.com/share?url=' . $uri . '&amp;text=' . $this->escape($this->item->unit_title) ?>" >
            <span class="glyphicon social-icon twitter"></span>
          </a>
          <a target="_blank" href="<?php echo 'https://plus.google.com/share?url=' . $uri ?>">
            <span class="glyphicon social-icon google-plus"></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>