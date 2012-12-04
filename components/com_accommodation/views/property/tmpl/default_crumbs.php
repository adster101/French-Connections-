<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if ($this->crumbs) : // Loop over the crumbs trail, if there is one  
  ?>
  <ul class="breadcrumb">

    <?php foreach ($this->crumbs as $key => $value) : ?>
    <?php if ($value->level > 0) : ?>
        <li>
          <a href="<?php echo JRoute::_('/index.php?option=com_propertysearch&view=search&q=' . $value->title)?>">
      <?php echo $value->title; ?>
          </a>
          <span class="divider">/</span>
        </li>
      <?php endif; ?>
  <?php endforeach; ?>
  </ul>
<?php endif; 