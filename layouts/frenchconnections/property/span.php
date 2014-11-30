<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$title = empty($displayData['title']) ? '' : (' title="' . $this->escape($displayData['title']) . '"');
$text = empty($displayData['text']) ? '' : ('<span class="j-links-link">' . $displayData['text'] . '</span>');
?>
<li>
	<a <?php echo $title; ?> rel="tooltip">
		<i class="icon-<?php echo $displayData['image']; ?>"></i> 
      <?php echo $text; ?>
	</a>
</li>
