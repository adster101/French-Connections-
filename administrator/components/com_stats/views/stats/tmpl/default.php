<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.popover');
?>
<form action="<?php echo JRoute::_('index.php?option=com_stats'); ?>" method="post" name="adminForm" id="adminForm" class="form-vertical">
  <?php echo JLayoutHelper::render('frenchconnections.search.default', array('view' => $this)); ?>
  <div id="j-main-container" class="row-fluid">
    <div class="span3">
      <div class="panel panel-default">
        <div class="panel-heading">
          <?php echo JText::_('COM_STATS_PAGE_VIEWS') ?>
          <span href="#" class="hasPopover pull-right lead" data-title="<?php echo JText::_('COM_STATS_PAGE_VIEWS') ?>" data-content="<?php echo JText::_('COM_STATS_PAGE_VIEWS_BLURB') ?>" data-placement="top">
            <i class="icon icon-info"></i>
          </span>
        </div>
        <div class="panel-body">
          <?php echo JText::sprintf('COM_STATS_PAGE_VIEWS_STAT', (int) $this->data['views'][0]) ?>
          <hr />
          <p class="align-right">
            <a href="<?php echo JRoute::_('index.php?option=com_rental&view=marketing') ?>">
              <?php echo JText::_('COM_STATE_PAGE_VIEWS_GET_MORE') ?>
            </a>
        </div>
      </div>
    </div>
    <div class="span3">
      <div class="panel panel-default">
        <div class="panel-heading">
          <?php echo JText::_('COM_STATS_ENQUIRIES') ?>
          <span href="#" class="hasPopover pull-right lead" data-title="<?php echo JText::_('COM_STATS_ENQUIRIES') ?>" data-content="<?php echo JText::_('COM_STATS_ENQUIRIES_BLURB') ?>" data-placement="top">
            <i class="icon icon-info"></i>
          </span>
        </div>  
        <div class="panel-body">
          <?php echo JText::sprintf('COM_STATS_ENQUIRIES_STAT', (int) $this->data['enquiries'][0]) ?>
          <hr />
          <p class="align-right">
            <a href="<?php echo JRoute::_('index.php?option=com_rental&view=marketing') ?>">
              <?php echo JText::_('COM_STATS_ENQUIRIES_GET_MORE') ?>
            </a>
          </p>
        </div>
      </div>
    </div>
    <div class="span3">
      <div class="panel panel-default">
        <div class="panel-heading">
          <?php echo JText::_('COM_STATS_WEBSITE_CLICKS') ?>
          <span href="#" class="hasPopover pull-right lead" data-title="<?php echo JText::_('COM_STATS_WEBSITE_CLICKS') ?>" data-content="<?php echo JText::_('COM_STATS_WEBSITE_CLICKS_BLURB') ?>" data-placement="top">
            <i class="icon icon-info"></i>
          </span>
        </div>
        <div class="panel-body">
          <?php echo JText::sprintf('COM_STATS_WEBSITE_CLICKS_STAT', (int) $this->data['clicks'][0]) ?>
          <hr />
          <p>&nbsp;</p>
        </div>
      </div>
    </div>

    <div class="span3">
      <div class="panel panel-default">
        <div class="panel-heading"> 
          <?php echo JText::_('COM_STATS_REVIEWS') ?>
          <span href="#" class="hasPopover pull-right lead" data-title="<?php echo JText::_('COM_STATS_REVIEWS') ?>" data-content="<?php echo JText::_('COM_STATS_REVIEWS_BLURB') ?>" data-placement="top">
            <i class="icon icon-info"></i>
          </span></div>
        <div class="panel-body">
          <?php echo JText::sprintf('COM_STATS_REVIEWS_STAT', (int) $this->data['reviews'][0]) ?>
          <hr />
          <p class="align-right">
            <a href="<?php echo JRoute::_('index.php?option=com_reviews') ?>">
              <?php echo JText::_('COM_STATS_REVIEWS_VIEW') ?>
            </a>
          </p>
        </div>
      </div>
    </div>
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>