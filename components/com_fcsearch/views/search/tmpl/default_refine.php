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
print_r($this->refine_options);
?>
<style>
  .refine.affix {top:5px;}
  
</style>
<div class="refine" data-spy="affix" data-offset-top="500">
  <h4><?php echo JText::_('COM_FCSEARCH_SEARCH_REFINE_SEARCH'); ?></h4>
  <div class="accordion" id="accordion2">
    <?php foreach ($this->refine_options as $key=>$values) : ?>    
    <div class="accordion-group">
      <div class="accordion-heading">
        <a class="accordion-toggle" data-toggle="collapse" href="#<?php echo $app->stringURLSafe($key) ?>">
          <?php echo $key; ?>
        </a>
      </div>
      <div id="<?php echo $app->stringURLSafe($key) ?>" class="accordion-body collapse in">
        <div class="accordion-inner">
          <?php foreach($values as $value) :?>
          <p>
            <a href="<?php echo JRoute::_(JUri::current()) . '/' . $value?>"><?php echo $value . '()'; ?></a>
          </p>
          
          <?php endforeach; ?>
        </div>
      </div>
    </div>
    
    <?php endforeach; ?>
 
  </div>





</div>

