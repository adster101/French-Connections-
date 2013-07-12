<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<ul class="nav nav-tabs">
  <li><a href="#top">Top</a></li>
  <li><a href="#description">Description</a></li>
  <li><a href="#location">Locality</a></li>
  <!--<li><a href="#travel">Travel</a></li>-->
  <!--<li><a href="#activities">Activities</a></li>-->
  <?php if ($this->reviews && count($this->reviews) > 0) : ?>
    <li><a href="#reviews">Reviews(<?php echo count($this->reviews) ?>)</a></li>
  <?php endif; ?>
  <li><a href="#facilities">Facilities</a></li>
  <li><a href="#availability">Availability</a></li>
  <li><a href="#tariffs">Tariffs</a></li>
  <li><a href="#email">Email owner</a></li>
</ul>
