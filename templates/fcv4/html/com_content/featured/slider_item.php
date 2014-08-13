<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

// Create a shortcut for params.
$params = &$this->item->params;
$images = json_decode($this->item->images);

$canEdit = $this->item->params->get('access-edit');
$info = $this->item->params->get('info_block_position', 0);

?>

<div class="item <?php echo ($this->item_number == 0) ? 'active' : '' ?>">
  <?php if (isset($images->image_slider) && !empty($images->image_slider)) : ?>
    <img src="<?php echo htmlspecialchars($images->image_slider); ?>" alt="<?php echo htmlspecialchars($images->image_slider_alt); ?>"/>
  <?php endif; ?>
  <div class="carousel-caption">
    <?php if ($params->get('show_title')) : ?>
      <h4 class="item-title">
        <?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
          <a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>"> <?php echo $this->escape($this->item->title); ?></a>
        <?php else : ?>
          <?php echo $this->escape($this->item->title); ?>
        <?php endif; ?>
      </h4>

    <?php endif; ?>

    <?php if ($this->item->state == 0) : ?>
      <span class="label label-warning"><?php echo JText::_('JUNPUBLISHED'); ?></span>
    <?php endif; ?>


    <?php
    if ($params->get('show_readmore') && $this->item->readmore) :
      if ($params->get('access-view')) :
        $link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
      else :
        $menu = JFactory::getApplication()->getMenu();
        $active = $menu->getActive();
        $itemId = $active->id;
        $link1 = JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId);
        $returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
        $link = new JUri($link1);
        $link->setVar('return', base64_encode($returnURL));
      endif;
      ?>

      <p>          
        <?php 
        echo JHtml::_('string.truncate', (strip_tags($this->item->text)), $params->get('readmore_limit')); ?>
        <a class="" href="<?php echo $link; ?>"> 
          <?php echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit')); ?>

          <span class="icon-chevron-right"></span>

        </a>
      </p>

        

    <?php endif; ?>
  </div>
</div>
