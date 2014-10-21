<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$text = $displayData['text'];
$url = $displayData['url'];

?>
<a href="<?php echo $url ?>" class="btn btn-small pull-right" target="_blank">
  <span class="icon-out-2"></span>
  <?php echo $text; ?>
</a>
