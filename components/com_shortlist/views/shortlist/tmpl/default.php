<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$params = $this->state->get('parameters.menu');
?>

<h1>
  <?php echo ($params->get('page_heading', '')) ? $params->get('page_heading') : $this->document->title; ?> (<?php echo count($this->items) ?>)
</h1>

<hr />
<?php if (count($this->items) > 0) : ?>

  <?php var_dump($this->items); ?>

<?php else: ?>

  <p class="lead"><?php echo JText::_('COM_SHORTLIST_SHORTLIST_EMPTY'); ?></p>


<?php endif; ?>


