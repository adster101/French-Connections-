<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$doTask   = $displayData['doTask'];
$class    = $displayData['class'];
$text     = $displayData['text'];
$btnClass = $displayData['btnClass'];

?>
<button type="button" onclick="<?php echo $doTask; ?>" class="<?php echo $btnClass; ?>" data-loading-text="<?php echo JText::_('FCGLOBAL_PLEASE_WAIT') ?>">
	<span class="<?php echo trim($class); ?>"></span>
	<?php echo $text; ?>
</button>
