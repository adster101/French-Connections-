<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

$lang = JFactory::getLanguage();
$lang->load('com_accommodation', JPATH_SITE, null, false, true);

?>
<div class="finder">
<h1>
  <?php echo $this->document->title; ?>
</h1>

	<div id="search-form" >
		<?php echo $this->loadTemplate('form'); ?>
	</div>

	<div id="search-results">
		<?php echo $this->loadTemplate('results'); ?>		

	</div>
</div>
