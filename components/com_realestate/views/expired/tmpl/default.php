<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$enquiry_data = $app->getUserState('com_accommodation.enquiry.data');
$Itemid_property = SearchHelper::getItemid(array('component', 'com_realestate'));
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');
?>
<div class="container">
  <h2 class="page-header">
    <?php echo $this->escape($this->document->title) ?>
  </h2>

  <?php echo JText::sprintf('COM_ACCOMMODATION_PROPERTY_NOT_FOUND_HEADER', $this->escape($this->item->unit_title)); ?>

  <?php $modules = JModuleHelper::getModules('postrealestateenquiry'); //If you want to use a different position for the modules, change the name here in your override.  ?>
  <div class="row">
    <div class="col-lg-6 col-md-6">
      <div class="well well-small">
        <h4><?php echo JText::_('COM_REALESTATE_RELATED_FORSALE') ?></h4>
        <p><?php echo JText::sprintf('COM_REALESTATE_RELATED_FORSALE_BLURB', $this->item->unit_title) ?></p>
        <?php foreach ($this->related as $key => $item) : ?>
          <?php
          $prices = JHtml::_('general.price', $item->price, $item->base_currency, '', '');
          $route = JRoute::_('index.php?option=com_realestate&Itemid=' . $Itemid_property . '&id=' . (int) $item->property_id);
          ?>
          <?php if ($item->title) : ?>     
            <div class="media">
              <a class="pull-left" href="<?php echo $route ?>">
                <img src='/images/property/<?php echo $item->property_id . '/thumb/' . $item->thumbnail ?>' />
              </a>
              <div class="media-body">
                <h4>
                  <a href="<?php echo $route ?>">
                    <strong><?php echo $this->escape($item->title); ?></strong> 
                  </a> 
                </h4>
                <p>
                  <?php echo JText::sprintf('COM_REALESTATE_SITE_UNIT_OCCUPANCY_BEDROOMS', $item->bedrooms); ?>
                  <?php if (!empty($item->price)) : ?> |&nbsp;&pound;<?php echo $prices['GBP'] ?>
                  <?php endif; ?>
                </p>     
              </div> 
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>    
    </div>
    <div class="col-lg-6 col-md-6">
      <?php foreach ($modules as $module) : // Render the cross-sell modules etc  ?>
        <?php echo JModuleHelper::renderModule($module, array('style' => 'rounded', 'id' => 'section-box')); ?>
      <?php endforeach; ?>
    </div>
  </div></div>