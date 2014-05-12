<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();

$enquiry_data = $app->getUserState('com_accommodation.enquiry.data');
$Itemid_property = FCSearchHelperRoute::getItemid(array('component', 'com_accommodation'));
JLoader::register('JHtmlGeneral', JPATH_SITE . '/libraries/frenchconnections/helpers/html/general.php');
?>
<h1>Enquiry sent</h1>


<?php echo JText::sprintf('COM_ACCOMMODATION_ENQUIRY_SENT_HEADER', $this->escape($enquiry_data['guest_forename']), $this->escape($this->item->unit_title)); ?>

<?php
//If you want to use a different position for the modules, change the name here in your override.
$modules = JModuleHelper::getModules('postenquiry');
?>
<div class="row-fluid">
  <div class="span6">
    <div class="well well-small">
      <h4><?php echo JText::_('COM_ACCOMMODATION_RELATED_RENTALS') ?></h4>
      <p><?php echo JText::sprintf('COM_ACCOMMODATION_RELATED_RENTALS_BLURB', $this->item->unit_title) ?></p>
      <?php foreach ($this->related as $key => $item) : ?>
        <?php
        $prices = JHtml::_('general.price', $item->price, $item->base_currency, '', '');
        $route = JRoute::_('index.php?option=com_accommodation&Itemid=' . $Itemid_property . '&id=' . (int) $item->id . '&unit_id=' . (int) $item->unit_id);
        ?>
        <?php if ($item->title) : ?>     
          <div class="clearfix">

               <p>
              <a href="<?php echo $route ?>">
                <img src='/images/property/<?php echo $item->unit_id . '/thumb/' . $item->thumbnail ?>' class="thumbnail img-rounded pull-left" />
              </a>
       
      
              <a href="<?php echo $route ?>">
                <strong><?php echo $this->escape($item->title); ?></strong> 
              </a> | 
              <?php echo JText::sprintf('COM_ACCOMMODATION_SITE_UNIT_OCCUPANCY_BEDROOMS', $item->occupancy, $item->bedrooms); ?>
              <?php if (!empty($item->price)) : ?> |&nbsp;&pound;<?php echo $prices['GBP'] ?>
              <?php endif; ?>

            </p>
          <?php endif; ?>
        </div> 
      <?php endforeach; ?>
    </div>    
  </div>

  <?php foreach ($modules as $module) : // Render the cross-sell modules etc ?>
    <?php echo JModuleHelper::renderModule($module, array('style' => 'rounded', 'id' => 'section-box')); ?>
  <?php endforeach; ?>
</div>