<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$title = $displayData['title'];
$url = $displayData['url'];

?>
<button data-toggle="modal" data-target="#collapseModal" class="btn btn-small" data-remote="<?php echo $url; ?>">
	<i class="icon-upload" title="<?php echo $title; ?>"></i>
	<?php echo $title; ?>
</button>
