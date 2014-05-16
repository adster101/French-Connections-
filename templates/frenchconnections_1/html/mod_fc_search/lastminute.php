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

$menus = $app->getMenu('site');

$Itemid = FCSearchHelperRoute::getItemid(array('component','com_fcsearch'));

$bedrooms = '';
$occupancy = '';
$arrival = '';
$departure = '';

// The layout for the anchor based navigation on the property listing
$search_layout = new JLayoutFile('search', $basePath = JPATH_SITE . '/components/com_fcsearch/layouts');
$search_data = new stdClass;
$search_data->searchterm = '';
$search_data->bedrooms = '';
$search_data->occupancy = '';
$search_data->arrival = '';
$search_data->departure = '';
$search_data->lastminute = $params->get('lastminute');


?>
<div class="well well-small clearfix">  
  <h4><?php echo JText::_('COM_FCSEARCH_ACCOMMODATION_SEARCH') ?></h4>

  <form id="property-search" action="<?php echo JRoute::_('index.php?option=com_fcsearch&lang=' . $lang . '&Itemid='. (int) $Itemid .'&s_kwds=' . JText::_('COM_FCSEARCH_S_KWDS_DEFAULT')) ?>" method="POST" class="form-vertical">
     <?php echo $search_layout->render($search_data); ?>

    <input type="hidden" name="option" value="com_fcsearch" />
  </form>
  
</div>