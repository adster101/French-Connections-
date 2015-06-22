<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$title = $displayData->title;
$target = $displayData->target;
$glyph = $displayData->glyph;
?>

<h4 class="panel-title">
  <span class="glyphicon glyphicon-<?php echo $glyph ?>"></span>&nbsp;
  <a 
    data-toggle="collapse"
    role="button" 
    href="#<?php echo $target ?>" 
    aria-expanded="true"
    aria-controls="<?php echo $target ?>">
      <?php echo $this->escape($title) ?>
  </a>
</h4>  