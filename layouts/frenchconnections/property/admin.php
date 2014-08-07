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
$property_id = $displayData['property_id'];
$unit_id = $displayData['unit_id'];

$url = '/listing/' . (int) $property_id . '?unit_id=' . (int) $unit_id . '&preview=1';

?>
<a href="<?php echo $url ?>" class="btn btn-small pull-right" target="_blank">
  <span class="icon-out-2"></span>
  <?php echo $text; ?>
</a>
