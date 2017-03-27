<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$uri = JUri::getInstance()->toString();

?>
<a class="pull-right" target="_blank" href="<?php
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
?>">
  <span class="glyphicon social-icon facebook"></span>
  <span class="sr-only">
    <?php echo JText::_('COM_ACCOMMODATION_FACEBOOK') ?>
  </span>
</a>

<a class="pull-right" target="_blank" href="<?php echo 'http://twitter.com/share?url=' . $uri . '&amp;text=' . $this->escape($this->item->unit_title) ?>" >
  <span class="glyphicon social-icon twitter"></span>
  <span class="sr-only">
    <?php echo JText::_('COM_ACCOMMODATION_TWITTER') ?>
  </span>
</a>

<a class="pull-right" target="_blank" href="<?php echo 'https://plus.google.com/share?url=' . $uri ?>">
  <span class="glyphicon social-icon google-plus"></span>
  <span class="sr-only">
    <?php echo JText::_('COM_ACCOMMODATION_GOOGLE_PLUS') ?>
  </span>
</a>
