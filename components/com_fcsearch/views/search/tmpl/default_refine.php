<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die;

$app = JFactory::getApplication();


$uri = str_replace('http://', '', JUri::current());

$refine_budget_min = $this->getBudgetFields();
$refine_budget_max = $this->getBudgetFields(250, 5000,250, 'max_');

$min_budget = $app->input->request->get('min');
$max_budget = $app->input->request->get('max');


?>


<div class="refine">
  
  <h4><?php echo JText::_('COM_FCSEARCH_SEARCH_REFINE_SEARCH'); ?></h4>



  <div class="accordion" id="accordion2">
         <div class="accordion-group">
        <div class="accordion-heading">
          <a class="accordion-toggle" data-toggle="collapse" href="#budget">
            <?php echo JText::_('COM_FCSEARCH_SEARCH_REFINE_BUDGET'); ?>
          </a>
        </div>
         <div id="budget" class="accordion-body collapse in">
          <div class="accordion-inner">    
  <div class="row-fluid">
    <div class="span6">
      <label for="min_price"><?php echo JText::_('COM_FCSEARCH_SEARCH_MINIMUM_PRICE_RANGE'); ?></label>
      <select id="min_price" name="min" class="span12">
        <?php echo JHtml::_('select.options', $refine_budget_min, 'value', 'text', $min_budget ); ?>      
      </select>
    </div>
    <div class="span6">
      <label for="max_price"><?php echo JText::_('COM_FCSEARCH_SEARCH_MAXIMUM_PRICE_RANGE'); ?></label>
      <select id="max_price" name="max" class="span12">
        <?php echo JHtml::_('select.options', $refine_budget_max, 'value', 'text', $max_budget ); ?>      
      </select>    
    </div>
  </div> 
          </div>
         </div>
         </div>
    
    
    
    <?php foreach ($this->refine_options as $key => $values) : ?>    
      <div class="accordion-group">
        <div class="accordion-heading">
          <a class="accordion-toggle" data-toggle="collapse" href="#<?php echo $app->stringURLSafe($key) ?>">
            <?php echo $key; ?>
          </a>
        </div>
        <div id="<?php echo $app->stringURLSafe($key) ?>" class="accordion-body collapse in">
          <div class="accordion-inner">
            <?php
            foreach ($values as $key => $value) :
              $tmp = array_flip(explode('/', $uri));
              $remove = false;

              $filter_string = $value['search_code'] . JStringNormalise::toUnderscoreSeparated(JApplication::stringURLSafe($key)) . '_' . $value['id'];

              if (array_key_exists($filter_string, $tmp)) {
                unset($tmp[$filter_string]);
                $new_uri = implode('/', array_flip($tmp));
                $remove = true;
              } else {
                $new_uri = implode('/', array_flip($tmp));
                $new_uri = $new_uri . '/' . $filter_string;
              }
              ?>
              <p>
                <a class="muted" href="<?php echo JRoute::_('http://' . $new_uri) ?>">
                  <i class="<?php echo ($remove ? 'icon-delete' : 'icon-new'); ?>"> </i>&nbsp;<?php echo $key; ?> (<?php echo $value['count']; ?>)
                </a>
              </p>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

