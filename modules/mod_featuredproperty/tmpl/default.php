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
<div class="row">
<?php foreach ($this->items as $item) { 
	if ($item->greeting) { ?>
		<div class="span2">	
			<?php	//print_r(json_decode($item->params)->access_options);die?>

			<a href="index.php?option=com_helloworld&view=helloworld&id=<?php echo $item->id ?>&lang=en">
				<img src="images/<?php echo $item->id ?>/thumb/thumbnail.jpg" />	
			</a>
		

			<h4><?php echo $item->greeting ?></h4>
			<h5><?php echo $item->title ?></h5>		
			<p><?php echo JText::_('MOD_FEATURED_PROPERTY_SLEEPS'); echo $item->occupancy; ?></p>
		</div>
	<?php } ?>
<?php } ?>	
</div>

