<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$inShortlist = $displayData->inShortlist;
$unit_id = $displayData->unit_id;
$action = $displayData->action;
$class = $displayData->class;

?>

<a class="shortlist<?php echo ($inShortlist) ? ' in-shortlist' : '' ?> <?php echo $class ?>"
   data-animation="false" 
   data-placement="left" 
   data-toggle="popover" 
   data-id='<?php echo $unit_id ?>' 
   data-content="<ul class='nav nav-pills nav-stacked'>
   <li><div class='checkbox'><label><input type='checkbox' <?php echo ($inShortlist) ? 'checked' : '';?> 
   value='<?php echo $inShortlist?>'> My Shortlist</input></label></div></li><li class='divider'><hr /></li><li>
   <a href='/my-account/shortlist'>View shortlist</a></li></ul>" 
   data-action='<?php echo $action ?>' href="#">
  <span class="glyphicon glyphicon-heart"></span>
  
</a>