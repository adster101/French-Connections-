<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<?php if ($this->crumbs) : // Loop over the crumbs trail, if there is one  ?>
  <ul class="breadcrumb">
    <?php foreach ($this->crumbs as $key => $value) : ?>
      <?php if ($value->level > 0) : ?>
        <li>
          <a href="<?php echo JRoute::_('index.php?option=com_fcsearch&Itemid=165&s_kwds=' . JApplication::stringURLSafe($value->title)) ?>">
            <?php echo $value->title; ?>
          </a>
          <span class="divider">/</span>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
    <li>
      <?php echo JText::sprintf('COM_ACCOMMODATION_BREADCRUMB_PROPERTY_ID',$this->escape($this->item->property_id)) ?>
    </li>
  </ul>
<?php endif; ?> 