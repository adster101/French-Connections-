<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_search
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$document = JFactory::getDocument();
$app = JFactory::getApplication();
$lang = $app->input->get('lang', 'en');
?>
<hr />
<div class='row-fluid'>
  <div class="span6">
    <p><strong><?php echo JText::_('COM_FCSEARCH_POPULAR_SEARCHES') ?></strong></p>
    <?php foreach ($popular as $k => $v) : ?>
      <p>
        <a href='<?php echo JRoute::_('index.php?option=com_fcsearch&s_kwds=' . $v->alias . '&lang=' . $lang . '&Itemid=165'); ?>'>
          <?php echo htmlspecialchars($v->title, ENT_QUOTES, 'UTF-8'); ?>
        </a>
      </p>
    <?php endforeach; ?>
  </div>
  <div class="span6"> 
    <p><strong><?php echo JText::_('COM_FCSEARCH_POPULAR_REGION_SEARCHES') ?></strong></p>
    <?php foreach ($regions as $region) : ?>
      <p>
        <a href="<?php echo JRoute::_('index.php?option=com_fcsearch&s_kwds=' . $region->alias . '&lang=' . $lang . '&Itemid=165'); ?>">
          <?php echo htmlspecialchars($region->title, ENT_QUOTES, 'UTF-8'); ?>

        </a>
      </p>
    <?php endforeach; ?>
  </div>

</div>


