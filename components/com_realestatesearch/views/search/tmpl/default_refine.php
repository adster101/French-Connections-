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
$pathway = $app->getPathway();
$items = $pathway->getPathWay();

$lang = $app->getLanguage()->getTag();

$uri = str_replace('http://', '', JUri::current());


$Itemid_search = SearchHelper::getItemid(array('component', 'com_realestatesearch'));

// The layout for the anchor based navigation on the property listing
$refine_type_layout = new JLayoutFile('refinetype', $basePath = JPATH_SITE . '/components/com_fcsearch/layouts');
?>

<h4 id="refine"><?php echo JText::_('COM_FCSEARCH_SEARCH_REFINE_SEARCH'); ?></h4>

<?php if ($this->localinfo->level) : ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      Location
      <?php //echo JText::_($this->escape($this->localinfo->title));  ?>
    </div>
    <div class="panel-body">
      <?php foreach ($items as $key => $value) : ?> 
        <?php if ($key > 0) : ?>
          <p>
            <a class="btn btn-sm btn-default" href="<?php echo JRoute::_($items[$key - 1]->link); ?>">
              <button class="close"> &times;</button>
              <?php echo $value->name = stripslashes(htmlspecialchars($value->name, ENT_COMPAT, 'UTF-8')); ?>
            </a>
          </p> 
          <?php if (($key + 1) == count($items)) : ?>
            <hr />
          <?php endif; ?>
        <?php endif; ?>

      <?php endforeach; ?>
      <?php if ($this->localinfo->level < 5) : ?>
        <p>Refine location</p>
        <?php if (!empty($this->location_options)) : ?>

          <?php
          $counter = 0;
          $hide = true;
          foreach ($this->location_options as $key => $value) :
            ?>
            <?php
            $remove = false;
            $tmp = explode('/', $uri); // Split the url out on the slash
            $filters = ($lang == 'en-GB') ? array_slice($tmp, 3) : array_slice($tmp, 4); // Remove the first 3 value of the URI
            $filters = (!empty($filters)) ? '/' . implode('/', $filters) : '';
            $route = 'index.php?option=com_realestatesearch&Itemid=' . $Itemid_search . '&s_kwds=' . JApplication::stringURLSafe($this->escape($value->title)) . $filters;
            ?>

            <?php if ($counter >= 5 && $hide) : ?>
              <?php $hide = false; ?>
              <div class="hide ">
              <?php endif; ?>
              <p>
                <a href="<?php echo JRoute::_($route) ?>">
                  <i class="muted <?php echo ($remove ? 'icon-delete' : 'icon-new'); ?>"> </i>
                  <?php echo $this->escape($value->title); ?> (<?php echo $value->count; ?>)
                </a>
              </p>      
              <?php $counter++; ?>
              <?php if ($counter == count($this->location_options) && !$hide) : ?>
              </div>
            <?php endif; ?>
            <?php if ($counter == count($this->location_options) && !$hide) : ?>
              <hr class="condensed" />
              <a href="#" class="show align-right" title="<?php echo JText::_('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS') ?>">
                <?php echo JText::_('COM_FCSEARCH_SEARCH_SHOW_MORE_OPTIONS'); ?></a>
            <?php endif; ?>
          <?php endforeach ?>
        <?php else : ?>
          <?php echo '...'; ?>
        <?php endif; ?> 
      <?php endif; ?>

    </div>
  </div>
<?php endif; ?>

