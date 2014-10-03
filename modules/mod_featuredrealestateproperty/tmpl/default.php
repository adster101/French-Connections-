<?php
/**
 * @package     Joomla.Tutorials
 * @subpackage  Module
 * @copyright   Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license     License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$lang = $app->input->get('lang', 'en');
// Register the Special Offers helper file
$Itemid_property = SearchHelper::getItemid(array('component', 'com_realestate'));
$Itemid_search = SearchHelper::getItemid(array('component', 'com_realestatesearch'));
?>

<div class="row">
  <?php foreach ($items as $key => $item) : ?>
    <?php
    $prices = JHtml::_('general.price', $item->price, $item->base_currency, '', '');
    $description = JHTml::_('string.truncate', $item->description, 125, true, false);
    $title = JText::sprintf('MOD_FEATURED_REALESTATE_PROPERTY_THUMB_TITLE', $item->id, $description);
    $region = JRoute::_('index.php?option=com_realestatesearch&s_kwds=' . $item->alias . '&lang=' . $lang . '&Itemid=' . (int) $Itemid_search);
    $property = JRoute::_('index.php?option=com_realestate&Itemid=' . (int) $Itemid_property . '&id=' . (int) $item->id);
    ?>
    <?php if ($item->title) : ?>     
      <div class="col-lg-3 col-sm-6"> 
        <p>
          <a title ="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" class="" href="<?php echo $property ?>">
            <img src='/images/property/<?php echo $item->id . '/thumb/' . $item->thumbnail ?>' class="fp-media-object" />
          </a>
        </p>
        <h4 class="fp-media-heading">
          <a href="<?php echo $region ?>">
            <?php echo htmlspecialchars($item->location); ?>
          </a>
        </h4>
        <p>
          <?php if (!empty($item->price)) : ?> 
          <p class="">
            <?php echo JText::sprintf('MOD_FEATURED_REALESTATE_PROPERTY_DETAIL', $prices['GBP'], $item->bedrooms, $item->bathrooms) ?>
            <a title ="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>" class="fp-thumbnail" href="<?php echo $property ?>">
              <?php echo JHTml::_('string.truncate', $item->title, 25, true, false); ?>&nbsp;&raquo;
            </a>
          </p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
</div>