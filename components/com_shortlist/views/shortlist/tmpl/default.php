<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$params = $this->state->get('parameters.menu');
$app = JFactory::getApplication();
$search_url = $app->getUserState('user.search');
$lang = JFactory::getLanguage();
$lang->load('com_accommodation', JPATH_SITE);
?>

<h1 class="page-header">
  <?php echo ($params->get('page_heading', '')) ? $params->get('page_heading') : $this->document->title; ?> (<?php echo $this->pagination->total ?>)
</h1>



<?php if (count($this->items) > 0) : ?>  
  <p><?php echo $this->pagination->getResultsCounter(); ?></p>

  <div class="search-results">


    <?php
    for ($i = 0, $n = count($this->items); $i < $n; $i++)
    {
      $this->result = &$this->items[$i];
      if (!empty($this->result->id))
      {
        echo $this->loadTemplate('shortlist');
      }
    }
    ?>
  </div>
  <div class="search-pagination">
    <div class="pagination">
      <?php echo $this->pagination->getPagesLinks(); ?>
    </div>
  </div>
<?php else: ?>

  <p class="lead"><?php echo JText::_('COM_SHORTLIST_SHORTLIST_EMPTY'); ?></p>


<?php endif; ?>


