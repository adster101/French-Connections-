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

?>


<div class="refine">
  <h4><?php echo JText::_('COM_FCSEARCH_SEARCH_REFINE_SEARCH'); ?></h4>
  <div class="accordion" id="accordion2">
    <?php foreach ($this->refine_options as $key => $values) : ?>    
      <div class="accordion-group">
        <div class="accordion-heading">
          <a class="accordion-toggle" data-toggle="collapse" href="#<?php echo $app->stringURLSafe($key) ?>">
            <?php echo $key; ?>
          </a>
        </div>
        <div id="<?php echo $app->stringURLSafe($key) ?>" class="accordion-body collapse in">
          <div class="accordion-inner">
            <?php foreach ($values as $key => $value) : 
              $tmp = array_flip(explode('/', $uri));
              $remove = false;
            
              $filter_string = $value['search_code'] . JStringNormalise::toUnderscoreSeparated($key) . '_' . $value['id'];

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

