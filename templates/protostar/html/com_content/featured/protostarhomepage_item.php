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
<?php if ($this->item->state == 0) : ?>
  <div class="system-unpublished">
  <?php endif; ?>
  <div class="item active">

    <?php if ($params->get('show_title')) : ?>
      <div class="carousel-caption">
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

      <?php if (!$params->get('show_intro')) : ?>
        <?php echo $this->item->event->afterDisplayTitle; ?>
      <?php endif; ?>
      <?php echo $this->item->event->beforeDisplayContent; ?> <?php echo $this->item->introtext; ?>

    </div>
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

      <p class="readmore"><a class="btn" href="<?php echo $link; ?>"> <span class="icon-chevron-right"></span>

          <?php
          if (!$params->get('access-view')) :
            echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
          elseif ($readmore = $this->item->alternative_readmore) :
            echo $readmore;
            if ($params->get('show_readmore_title', 0) != 0) :
              echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
            endif;
          elseif ($params->get('show_readmore_title', 0) == 0) :
            echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');
          else :
            echo JText::_('COM_CONTENT_READ_MORE');
            echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
          endif;
          ?>

        </a></p>

  <?php endif; ?>

  <?php if ($this->item->state == 0) : ?>
    </div>
  <?php endif; ?>
  </div>
<?php echo $this->item->event->afterDisplayContent; ?>
