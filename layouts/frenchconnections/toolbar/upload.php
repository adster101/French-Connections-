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
$target = $displayData['target'];

?>
<button data-toggle="modal" data-target="<?php echo $target ?>" class="btn btn-small">
	<i class="icon-upload" title="<?php echo $title; ?>"></i>
	<?php echo $title; ?>
</button>
