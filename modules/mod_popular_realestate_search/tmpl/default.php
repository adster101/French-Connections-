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
$Itemid = SearchHelper::getItemid(array('component', 'com_realestatesearch'));
?>
<hr />
<div class='row'>
  <div class="col-lg-6 col-md-6 col-sm-6">
    <h3>
      <?php echo JText::_('COM_REALESTATE_SEARCH_POPULAR_SEARCHES') ?>
    </h3>
    <?php foreach ($popular as $k => $v) : ?>
      <p>
        <a href='<?php echo JRoute::_('index.php?option=com_realestatesearch&s_kwds=' . $v->alias . '&lang=' . $lang . '&Itemid=' . (int) $Itemid); ?>'>
          <?php echo htmlspecialchars($v->title, ENT_QUOTES, 'UTF-8'); ?>
        </a>
      </p>
    <?php endforeach; ?>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6"> 
    <h3>
      <?php echo JText::_('COM_REALESTATE_SEARCH_POPULAR_REGION_SEARCHES') ?>
    </h3>
    <?php foreach ($regions as $region) : ?>
      <p>
        <a href="<?php echo JRoute::_('index.php?option=com_realestatesearch&s_kwds=' . $region->alias . '&lang=' . $lang . '&Itemid=' . (int) $Itemid); ?>">
          <?php echo htmlspecialchars($region->title, ENT_QUOTES, 'UTF-8'); ?>

        </a>
      </p>
    <?php endforeach; ?>
  </div>

</div>



