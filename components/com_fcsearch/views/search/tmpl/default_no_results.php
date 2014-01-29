<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<p class='lead'>
                <strong><?php echo JText::_('COM_FCSEARCH_SEARCH_NO_RESULTS_HEADING'); ?></strong>
              </p>
              <p><?php echo JText::_('COM_FCSEARCH_SEARCH_NO_RESULTS_BODY'); ?></p>
<?php

// Load the most popular search module 
$module = JModuleHelper::getModule('mod_popular_search');
echo JModuleHelper::renderModule($module);

?>

