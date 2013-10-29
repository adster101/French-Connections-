<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$input = JFactory::getApplication()->input;
$preview = $input->get('preview','','int');

$link = 'index.php?option=com_accommodation&Itemid=259&id=' . (int) $this->item->id . '&unit_id=' . (int) $this->item->unit_id;

if ((int) $preview && $preview == 1 ) {
  $link .= '&preview=1';
}

$route = JRoute::_( $link ) ;

?>

<ul class="nav nav-tabs">
  <li><a href="<?php echo $route ?>#top">Top</a></li>
  <li><a href="<?php echo $route ?>#description">Description</a></li>
  <li><a href="<?php echo $route ?>#location">Locality</a></li>
  <!--<li><a href="#travel">Travel</a></li>-->
  <li><a href="<?php echo $route ?>#activities">Activities</a></li>
  <?php if ($this->reviews && count($this->reviews) > 0) : ?>
    <li><a href="<?php echo $route ?>#reviews">Reviews(<?php echo count($this->reviews) ?>)</a></li>
  <?php endif; ?>
  <li><a href="<?php echo $route ?>#facilities">Facilities</a></li>
  <li><a href="<?php echo $route ?>#availability">Availability</a></li>
  <li><a href="<?php echo $route ?>#tariffs">Tariffs</a></li>
  <li><a href="<?php echo $route ?>#email">Email owner</a></li>
</ul>
