<?php
 
/**
 * @package     Joomla.Tutorials
 * @subpackage  Module
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access to this file
defined('_JEXEC') or die;


?>
<ul class="thumbnails">
<?php foreach ($this->items as $item) { ?>
	<li class="span2">
		<div class="thumbnail">
			<a href="#" class="">
				<img src="/images/<?php echo $item->id ?>/thumbnail.jpg" />	
			</a>
			<h4><?php echo $item->greeting ?></h4>
			<p><?php echo JText::_('MOD_FEATURED_PROPERTY_SLEEPS'.$item->occupancy) ?></p>
		</div>
	</li>
<?php } ?>
</ul>

