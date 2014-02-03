<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$params = $this->state->get('parameters.menu');
$app = JFactory::getApplication();
$search_url = $app->getUserState('user.search');
$lang = JFactory::getLanguage();
$lang->load('com_accommodation', JPATH_SITE);
?>

<div class="page-header">
  <div class="row-fluid">
    <div class="span9">
      <h1>
        <?php echo ($params->get('page_heading', '')) ? $params->get('page_heading') : $this->document->title; ?> (<?php echo $this->pagination->total ?>)
      </h1>
    </div>
    <?php if (!empty($search_url)) : ?>
      <div class="span3">
        <a class="btn btn-large" href="<?php echo $search_url ?>" title="">    
          <i class="icon icon-backward-2"></i>
          <?php echo JText::_('COM_ACCOMMODATION_BACK_TO_SEARCH_RESULTS'); ?>
        </a>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php if (count($this->items) > 0) : ?>

  <div class="row-fluid">
    <div class="span9">
      <div class="search-pagination hidden-phone">
        <div class="pagination small ">
          <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
      </div>
    </div>
    <div class="span3">
      <p class="" style="line-height:34px;"><?php echo $this->pagination->getResultsCounter(); ?></p>
    </div>
  </div>
  <ul class="search-results list-striped">
    <?php
    for ($i = 0, $n = count($this->items); $i < $n; $i++) {
      $this->result = &$this->items[$i];
      if (!empty($this->result->id)) {
        echo $this->loadTemplate('shortlist');
      }
    }
    ?>
  </ul>
  <div class="search-pagination">
    <div class="pagination">
      <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
  </div>
<?php else: ?>

  <p class="lead"><?php echo JText::_('COM_SHORTLIST_SHORTLIST_EMPTY'); ?></p>

  <p>Popular searches</p>

<?php endif; ?>


