<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>



<ul class="nav nav-tabs">
  <?php foreach($this->units as $unit) : ?>
  <li>
    <a href="#">
      <?php echo $unit->title; ?>
    </a>
  </li>

  <?php endforeach; ?>
</ul>
