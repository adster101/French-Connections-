<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$uri = $displayData->route;
$appid = $displayData->appid;
$class = $displayData->class;
$title = $displayData->title;
$header = $displayData->header;
$description = '';

?>
<?php if ($header) :?>
<h4><?php echo JText::_('SHARE') ?></h4>
  <?php endif;?> 
 <div class="<?php echo $class ?>"> 
          <a target="_blank" href="<?php
          echo 'https://www.facebook.com/dialog/feed?app_id=612921288819888&display=page&href='
          . urlencode($uri)
          . '&redirect_uri='
          . urlencode($uri)
          . '&picture='
          . '&name=' . urlencode($title)
          . '&description=' . urlencode(JHtml::_('string.truncate', $description, 100, true, false));
          ?>"
             <span class="glyphicon social-icon facebook"></span>
          </a> 
          <a target="_blank" href="<?php echo 'http://twitter.com/share?url=' . $uri . '&amp;text=' . $this->escape($title) ?>" >
            <span class="glyphicon social-icon twitter"></span>
          </a>
          <a target="_blank" href="<?php echo 'https://plus.google.com/share?url=' . $uri ?>">
            <span class="glyphicon social-icon google-plus"></span>
          </a>
        </div>