<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$id = empty($displayData['id']) ? '' : (' class="' . $displayData['id'] . '"');
$target = empty($displayData['target']) ? '' : (' target="' . $displayData['target'] . '"');
$onclick = empty($displayData['onclick']) ? '' : (' onclick="' . $displayData['onclick'] . '"');
$title = empty($displayData['title']) ? '' : (' title="' . $this->escape($displayData['title']) . '"');
$text = empty($displayData['text']) ? '' : ($displayData['text']);
$link = empty($displayData['link']) ? '' : (JFilterOutput::ampReplace($displayData['link']));
?>
<li<?php echo $id; ?>>
  <?php if (!empty($link)) : ?>
    <a href="<?php echo $link ?>" <?php echo $title ?> rel="tooltip">
    <?php else: ?>
      <a <?php echo $title; ?> class="" rel="tooltip"> 
      <?php endif; ?>
      <i class="icon-<?php echo $displayData['image']; ?>"></i> 
      <?php echo $text; ?>
    </a>

</li>
