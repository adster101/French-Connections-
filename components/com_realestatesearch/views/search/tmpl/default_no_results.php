<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
<div class="container">
  <p class='lead'>
    <strong><?php echo JText::_('COM_FCSEARCH_SEARCH_NO_RESULTS_HEADING'); ?></strong>
  </p>
  <p><?php echo JText::_('COM_REALESTATESEARCH_SEARCH_NO_RESULTS_BODY'); ?></p>
<?php
// Load the most popular search module 
$module = JModuleHelper::getModule('mod_popular_realestate_search');
echo JModuleHelper::renderModule($module, array('cache'));
?>

</div>
