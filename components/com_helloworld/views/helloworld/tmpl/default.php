<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$language = JFactory::getLanguage();
$language->load('com_helloworld', JPATH_ADMINISTRATOR, 'en-GB', true);
?>
<h1><?php echo $this->item->greeting.(($this->item->category and $this->item->params->get('show_category')) ? (' ('.$this->item->category.')') : ''); ?></h1>

<?php 
	foreach ($this->item->params->getValue('internal_features') as $poop) {
		echo JText::_($poop);
	}

	
