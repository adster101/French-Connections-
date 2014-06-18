<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$doTask = $displayData['doTask'];
$title = $displayData['title'];
$remote = $displayData['remote'];
$id = $displayData['id'];
$icon = $displayData['icon'];
?>
<button data-toggle="modal" data-target="#<?php echo $id ?>" href="<?php echo $remote; ?>" class="btn btn-small">
  <i class="icon-<?php echo $icon ?>" title="<?php echo $title; ?>"></i>
  <?php echo $title; ?>
</button>
